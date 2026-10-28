<?php

namespace Laraditz\Lazada\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaMessage;
use PHPUnit\Framework\Attributes\Test;

class RequestLoggingTest extends TestCase
{
    private function exchange(): array
    {
        Http::fake([
            'https://auth.lazada.com/rest/*' => Http::response([
                'code' => '0',
                'request_id' => 'req-1',
                'access_token' => 'real-at',
                'refresh_token' => 'real-rt',
                'expires_in' => 3600,
                'refresh_expires_in' => 7200,
                'country_user_info' => [[
                    'seller_id' => 555,
                    'user_id' => 'u1',
                    'country' => 'my',
                    'short_code' => 'NEWSHOP',
                ]],
            ]),
            '*/seller/get*' => Http::response(['code' => '0', 'request_id' => 'req-2', 'data' => ['name' => 'Shop']]),
        ]);

        return (new Lazada())->auth()->accessToken(code: 'real-code');
    }

    private function row(): object
    {
        return DB::table('lazada_messages')->where('action', 'AuthService::accessToken')->first();
    }

    #[Test]
    public function test_default_logs_unredacted_request()
    {
        $this->exchange();

        $this->assertSame(1, DB::table('lazada_messages')->where('action', 'AuthService::accessToken')->count());
        $this->assertSame('real-code', json_decode($this->row()->request, true)['code']);
        $this->assertSame('real-at', json_decode($this->row()->response, true)['access_token']);
        $this->assertStringNotContainsString('REDACTED', $this->row()->request);
    }

    #[Test]
    public function test_missing_logging_config_behaves_as_default()
    {
        config()->set('lazada.logging', null);
        $this->exchange();

        $this->assertSame(1, DB::table('lazada_messages')->where('action', 'AuthService::accessToken')->count());
        $this->assertSame('real-code', json_decode($this->row()->request, true)['code']);
        $this->assertSame('real-at', json_decode($this->row()->response, true)['access_token']);
    }

    #[Test]
    public function test_logging_disabled_writes_no_row_but_access_token_is_stored()
    {
        config()->set('lazada.logging.requests', false);
        $this->exchange();

        $this->assertSame(0, DB::table('lazada_messages')->count());
        $this->assertSame(1, DB::table('lazada_access_tokens')->count());
        $this->assertSame('real-at', DB::table('lazada_access_tokens')->value('access_token'));
    }

    #[Test]
    public function test_redaction_masks_request_response_and_url()
    {
        config()->set('lazada.logging.redact_secrets', true);
        $result = $this->exchange();

        $this->assertSame('real-at', $result['access_token']);

        $row = $this->row();
        $request = json_decode($row->request, true);
        $response = json_decode($row->response, true);

        $this->assertSame('[REDACTED]', $request['code']);
        $this->assertSame('[REDACTED]', $request['sign']);
        $this->assertSame('test_app_key', $request['app_key']);
        $this->assertSame('[REDACTED]', $response['access_token']);
        $this->assertSame('[REDACTED]', $response['refresh_token']);
        $this->assertSame(3600, $response['expires_in']);
        $this->assertStringNotContainsString('real-', $row->request . $row->response . $row->url);

        // the seller lookup after the exchange also logs, with its access_token masked
        $seller = DB::table('lazada_messages')->where('action', 'SellerService::get')->first();
        $this->assertSame('[REDACTED]', json_decode($seller->request, true)['access_token']);
    }

    #[Test]
    public function test_redaction_keeps_real_auth_code_on_token()
    {
        config()->set('lazada.logging.redact_secrets', true);
        $this->exchange();

        $this->assertSame('real-code', DB::table('lazada_access_tokens')->value('code'));
    }

    #[Test]
    public function test_prunable_disabled_by_default()
    {
        LazadaMessage::create(['action' => 'X::y', 'url' => 'u']);
        DB::table('lazada_messages')->update(['created_at' => now()->subDays(400)]);

        $this->assertSame(0, (new LazadaMessage)->prunable()->count());

        config()->set('lazada.logging.prune_after_days', 0);
        $this->assertSame(0, (new LazadaMessage)->prunable()->count());
    }

    #[Test]
    public function test_prunable_selects_rows_older_than_configured_days()
    {
        config()->set('lazada.logging.prune_after_days', 30);

        LazadaMessage::create(['action' => 'X::old', 'url' => 'u']);
        LazadaMessage::create(['action' => 'X::new', 'url' => 'u']);
        DB::table('lazada_messages')->where('action', 'X::old')->update(['created_at' => now()->subDays(31)]);

        $this->assertSame(['X::old'], (new LazadaMessage)->prunable()->pluck('action')->all());
        $this->assertSame(1, (new LazadaMessage)->pruneAll());
        $this->assertSame(1, LazadaMessage::count());
    }
}

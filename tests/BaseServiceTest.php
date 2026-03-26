<?php

namespace Laraditz\Lazada\Tests;

use Illuminate\Support\Facades\Http;
use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaAccessToken;
use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Models\LazadaSeller;
use Laraditz\Lazada\Services\AuthService;
use Laraditz\Lazada\Services\OrderService;

class BaseServiceTest extends TestCase
{
    private static $latestResponse;

    public function test_get_common_parameters_reads_access_token_from_lazada_seller(): void
    {
        $seller = LazadaSeller::factory()->create(['short_code' => 'TESTSHOP']);
        LazadaAccessToken::factory()->create([
            'subjectable_type' => LazadaSeller::class,
            'subjectable_id' => $seller->id,
            'access_token' => 'tok_abc123',
        ]);

        $lazada = new Lazada(seller_short_code: 'TESTSHOP');
        $lazada->checkSeller();

        $service = new OrderService(lazada: $lazada);
        $params = $service->getCommonParameters();

        $this->assertSame('tok_abc123', $params['access_token']);
    }

    public function test_execute_writes_seller_id_to_lazada_message(): void
    {
        Http::fake(['*' => Http::response(['code' => '0', 'data' => [], 'request_id' => 'r1'], 200)]);

        $seller = LazadaSeller::factory()->create(['short_code' => 'TESTSHOP']);
        LazadaAccessToken::factory()->create([
            'subjectable_type' => LazadaSeller::class,
            'subjectable_id' => $seller->id,
            'access_token' => 'tok_abc123',
        ]);

        $lazada = new Lazada(seller_short_code: 'TESTSHOP');
        $lazada->checkSeller();

        try {
            $service = new OrderService(lazada: $lazada);
            $service->list(created_after: '2024-01-01T00:00:00+08:00');
        } catch (\Throwable $e) {
            // May fail due to missing region URL — message record is still written
        }

        $message = LazadaMessage::latest()->first();
        $this->assertNotNull($message);
        $this->assertEquals($seller->id, $message->seller_id);
    }

    public function test_get_common_parameters_skips_seller_for_auth_service(): void
    {
        config(['lazada.seller_short_code' => null]);
        $lazada = new Lazada(); // no seller configured
        $authService = new AuthService(lazada: $lazada);
        $params = $authService->getCommonParameters();

        $this->assertArrayNotHasKey('access_token', $params);
    }
}

<?php

namespace Laraditz\Lazada\Tests;

use Illuminate\Support\Facades\Http;
use Laraditz\Lazada\Exceptions\LazadaTokenException;
use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Services\AuthService;

class AuthServiceTest extends TestCase
{
    public static $latestResponse;

    public function test_authorization_url_does_not_url_encode_redirect_uri(): void
    {
        $lazada = new Lazada(app_callback_url: 'https://example.com/lazada/seller/authorized');

        $service = new AuthService(lazada: $lazada);

        $this->assertSame(
            'https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri=https://example.com/lazada/seller/authorized&client_id=test_app_key',
            $service->authorizationUrl()
        );
    }

    private function tokenResponse(?int $sellerId): array
    {
        return [
            'code' => '0',
            'request_id' => 'req-1',
            'access_token' => 'token-555',
            'refresh_token' => 'refresh-555',
            'expires_in' => 3600,
            'refresh_expires_in' => 7200,
            'country_user_info' => [[
                'seller_id' => $sellerId,
                'user_id' => 'u1',
                'country' => 'my',
                'short_code' => 'NEWSHOP',
            ]],
        ];
    }

    public function test_access_token_hook_fetches_new_sellers_info(): void
    {
        Http::fake([
            'https://auth.lazada.com/rest/*' => Http::response($this->tokenResponse(555)),
            '*/seller/get*' => Http::response(['code' => '0', 'request_id' => 'req-2', 'data' => ['name' => 'New Shop']]),
        ]);

        (new Lazada())->auth()->accessToken(code: 'abc');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/seller/get')
                && $request['access_token'] === 'token-555';
        });

        $this->assertSame(
            555,
            (int) LazadaMessage::where('action', 'SellerService::get')->value('seller_id')
        );
    }

    public function test_missing_seller_id_throws_token_exception(): void
    {
        Http::fake([
            'https://auth.lazada.com/rest/*' => Http::response($this->tokenResponse(null)),
        ]);

        $this->expectException(LazadaTokenException::class);

        (new Lazada())->auth()->accessToken(code: 'abc');
    }
}

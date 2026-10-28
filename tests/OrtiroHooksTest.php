<?php

namespace Laraditz\Lazada\Tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laraditz\Lazada\Events\SellerAuthorized;
use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaAccessToken;
use Laraditz\Lazada\Models\LazadaSeller;
use Laraditz\Lazada\Services\AuthService;

class OrtiroHooksTest extends TestCase
{
    private function service(): AuthService
    {
        return new AuthService(lazada: new Lazada(app_callback_url: 'https://example.com/cb'));
    }

    public function test_authorization_url_default_unchanged(): void
    {
        $this->assertSame(
            'https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri=https://example.com/cb&client_id=test_app_key',
            $this->service()->authorizationUrl()
        );
    }

    public function test_authorization_url_overrides(): void
    {
        $url = $this->service()->authorizationUrl('https://other.test/x?y=1', 'a b&c');

        $this->assertSame(
            'https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri=https://other.test/x?y=1&client_id=test_app_key&state=a+b%26c',
            $url
        );
    }

    public function test_push_signature_valid(): void
    {
        $lazada = new Lazada();
        $body = '{"message_type":1}';

        $this->assertTrue($lazada->verifyPushSignature($body, $lazada->getWebPushSignature($body)));
    }

    public function test_push_signature_case_insensitive_header(): void
    {
        $lazada = new Lazada();
        $body = '{"message_type":1}';

        $this->assertTrue($lazada->verifyPushSignature($body, '  ' . strtolower($lazada->getWebPushSignature($body)) . ' '));
    }

    public function test_push_signature_rejects_tampered_body(): void
    {
        $lazada = new Lazada();
        $sig = $lazada->getWebPushSignature('{"a":1}');

        $this->assertFalse($lazada->verifyPushSignature('{"a":2}', $sig));
        $this->assertFalse($lazada->verifyPushSignature('{"a":1}', ''));
    }

    public function test_access_token_dispatches_seller_authorized(): void
    {
        Event::fake([SellerAuthorized::class]);
        Http::fake([
            'https://auth.lazada.com/rest/*' => Http::response([
                'code' => '0',
                'access_token' => 'token-777',
                'refresh_token' => 'refresh-777',
                'expires_in' => 3600,
                'refresh_expires_in' => 7200,
                'country_user_info' => [['seller_id' => 777, 'user_id' => 'u1', 'country' => 'my', 'short_code' => 'S']],
            ]),
            '*/seller/get*' => Http::response(['code' => '0', 'data' => ['name' => 'Shop']]),
        ]);

        (new Lazada())->auth()->accessToken(code: 'abc');

        Event::assertDispatched(SellerAuthorized::class, function (SellerAuthorized $e) {
            return $e->seller instanceof LazadaSeller
                && (int) $e->seller->id === 777
                && $e->accessToken instanceof LazadaAccessToken
                && $e->accessToken->access_token === 'token-777';
        });
    }
}

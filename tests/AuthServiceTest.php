<?php

namespace Laraditz\Lazada\Tests;

use Laraditz\Lazada\Lazada;
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
}

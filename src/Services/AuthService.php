<?php

namespace Laraditz\Lazada\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laraditz\Lazada\Events\SellerAuthorized;
use Laraditz\Lazada\Exceptions\LazadaTokenException;
use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaAccessToken;
use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Models\LazadaSeller;

class AuthService extends BaseService
{
    /**
     * Return authorization URL to get the code to generate access token
     */
    public function authorizationUrl(?string $redirectUri = null, ?string $state = null): string
    {
        $app_key = $this->lazada->getAppKey();
        $app_callback_url = $redirectUri ?? $this->lazada->getAppCallbackUrl();
        $authorization_url = config('lazada.authorization_url');

        $authorization_url = Str::swap([
            ':app_key' => urlencode($app_key),
            // Lazada rejects an url-encoded redirect_uri
            ':callback_url' => $app_callback_url,
        ], $authorization_url);

        if ($state !== null) {
            $authorization_url .= '&state=' . urlencode($state);
        }

        return $authorization_url;
    }

    public function afterAccessTokenRequest(LazadaMessage $request, array $result = []): void
    {
        $seller = DB::transaction(function () use ($request, $result) {

            $user_info = data_get($result, 'country_user_info.0');
            $seller_id = data_get($user_info, 'seller_id');

            throw_if(!$seller_id, LazadaTokenException::class, __('Missing seller_id'));

            $seller = LazadaSeller::updateOrCreate(
                ['id' => $seller_id],
                [
                    'user_id' => data_get($user_info, 'user_id'),
                    'country_code' => data_get($user_info, 'country') ? strtoupper(data_get($user_info, 'country')) : null,
                    'short_code' => data_get($user_info, 'short_code'),
                ]
            );

            $commonData = [
                'access_token' => data_get($result, 'access_token'),
                'refresh_token' => data_get($result, 'refresh_token'),
                'expires_at' => now()->addSeconds(data_get($result, 'expires_in')),
                'refresh_expires_at' => now()->addSeconds(data_get($result, 'refresh_expires_in')),
                'code' => data_get($request, 'request.code'),
            ];

            if ($seller->accessToken) {
                $seller->accessToken->update($commonData);
            } else {
                $seller->accessToken()->create([
                    ...$commonData,
                    'user_info' => $user_info,
                    'country_code' => data_get($result, 'country') ? strtoupper(data_get($result, 'country')) : null,
                    'account_id' => data_get($result, 'account_id'),
                    'account' => data_get($result, 'account'),
                    'account_platform' => data_get($result, 'account_platform'),
                ]);
            }

            return $seller;
        });

        if ($seller) {
            $seller->load('accessToken');

            if ($seller->accessToken) {
                SellerAuthorized::dispatch($seller, $seller->accessToken);
            }
        }

        // one time update seller info, its ok if failed
        if ($seller && $seller->name === null) {

            try {
                Lazada::make(
                    region: $this->lazada->getRegion(),
                    app_key: $this->lazada->getAppKey(),
                    app_secret: $this->lazada->getAppSecret(),
                    app_callback_url: $this->lazada->getAppCallbackUrl(),
                    sign_method: $this->lazada->getSignMethod(),
                    sandbox_mode: $this->lazada->getSandboxMode(),
                    seller_id: (string) $seller->id,
                )->seller()->get();
            } catch (\Throwable $th) {
                // throw $th;
            }
        }
    }

    public function afterRefreshTokenRequest(LazadaMessage $request, array $result = []): void
    {
        $user_info = data_get($result, 'country_user_info_list.0');
        $seller_id = data_get($user_info, 'seller_id');
        $access_token = data_get($result, 'access_token');
        $refresh_token = data_get($result, 'refresh_token');

        if ($seller_id && $access_token && $refresh_token) {

            $seller = LazadaSeller::findOrFail($seller_id);

            $commonData = [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'expires_at' => now()->addSeconds(data_get($result, 'expires_in')),
                'refresh_expires_at' => now()->addSeconds(data_get($result, 'refresh_expires_in')),
            ];

            if ($seller->accessToken) {
                $seller->accessToken->update($commonData);
            } else {
                $seller->accessToken()->create([
                    ...$commonData,
                    'user_info' => $user_info,
                    'country_code' => data_get($result, 'country') ? strtoupper(data_get($result, 'country')) : null,
                    'account_id' => data_get($result, 'account_id'),
                    'account' => data_get($result, 'account'),
                    'account_platform' => data_get($result, 'account_platform'),
                ]);
            }
        }
    }
}

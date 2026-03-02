<?php

namespace Laraditz\Lazada;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LogicException;

class Lazada
{
    private $services = ['auth', 'seller', 'order', 'helper', 'finance', 'product'];

    public function __construct(
        private ?string $region = null,
        private ?string $app_key = null,
        private ?string $app_secret = null,
        private ?string $app_callback_url = null,
        private ?string $sign_method = 'sha256',
        private ?bool $sandbox_mode = false,
        private ?string $seller_id = null,
    ) {
        $this->setAppKey($this->app_key ?? config('lazada.app_key'));
        $this->setAppSecret($this->app_secret ?? config('lazada.app_secret'));
        $this->setSellerId($this->seller_id ?? config('lazada.seller_id'));
        $this->setAppCallbackUrl($this->app_callback_url ?? config('lazada.app_callback_url'));
    }

    public static function make(...$args): static
    {
        return new static(...$args);
    }

    public function __call($method, $arguments)
    {
        throw_if(! $this->getAppKey(), LogicException::class, __('Missing App Key.'));
        throw_if(! $this->getAppSecret(), LogicException::class, __('Missing App Secret.'));

        if (count($arguments) > 0) {
            $argumentCollection = collect($arguments);

            try {
                $argumentCollection->keys()->ensure('string');
            } catch (\Throwable $th) {
                // throw $th;
                throw new LogicException(__('Please pass a named arguments in :method method.', ['method' => $method]));
            }

            if ($seller_id = data_get($arguments, 'seller_id')) {
                $this->setSellerId($seller_id);
            }
        }

        throw_if(! ($this->getSellerId() || in_array($method, ['auth'])), LogicException::class, __('Missing Seller ID.'));

        $property_name = strtolower(Str::snake($method));

        if (in_array($property_name, $this->services)) {
            $reformat_property_name = ucfirst(Str::camel($method));

            $service_name = 'Laraditz\\Lazada\\Services\\'.$reformat_property_name.'Service';

            return new $service_name(lazada: app('lazada'));
        } else {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                get_class(),
                $method
            ));
        }
    }

    public function getSignature(string $route, array $payload): string
    {
        $app_secret = $this->getAppSecret();
        $sign_method = $this->getSignMethod();

        ksort($payload);

        $signature = urldecode(Arr::query($payload));

        $signature = $route.Str::remove(['=', '&'], $signature);

        $signature = hash_hmac($sign_method, $signature, $app_secret);

        return strtoupper($signature);
    }

    public function getWebPushSignature(string $body): string
    {
        $app_key = $this->getAppKey();
        $app_secret = $this->getAppSecret();
        $sign_method = $this->getSignMethod();
        $base = $app_key.$body;

        $signature = hash_hmac($sign_method, $base, $app_secret);

        return strtoupper($signature);
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string|int $region): void
    {
        $this->region = $region;
    }

    public function getAppKey(): string
    {
        return $this->app_key;
    }

    public function setAppKey(string|int $appKey): void
    {
        $this->app_key = $appKey;
    }

    public function getAppSecret(): string
    {
        return $this->app_secret;
    }

    public function setAppSecret(string|int $appSecret): void
    {
        $this->app_secret = $appSecret;
    }

    public function getAppCallbackUrl(): string
    {
        return $this->app_callback_url ?? route('lazada.seller.authorized');
    }

    public function setAppCallbackUrl(string|int $appCallbackUrl): void
    {
        $this->app_callback_url = $appCallbackUrl;
    }

    public function getSignMethod(): string
    {
        return $this->sign_method;
    }

    public function getSandboxMode(): bool
    {
        return $this->sandbox_mode;
    }

    public function setSellerId(string $seller_id): void
    {
        $this->seller_id = $seller_id;
    }

    public function getSellerId(): ?string
    {
        return $this->seller_id;
    }
}

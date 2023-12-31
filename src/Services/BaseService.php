<?php

namespace Laraditz\Lazada\Services;

use BadMethodCallException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laraditz\Lazada\Exceptions\LazadaAPIError;
use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Models\LazadaSeller;
use LogicException;

class BaseService
{
    public string $methodName;

    public string $serviceName;

    public string $sellerId;

    public function __construct(
        public Lazada $lazada,
        private ?string $route = '',
        private ?string $method = 'get',
        private ?array $queryString = [],
        private ?array $payload = [],
    ) {
    }

    public function __call($methodName, $arguments)
    {
        $oClass = new \ReflectionClass(get_called_class());
        $fqcn = $oClass->getName();
        $this->serviceName = $oClass->getShortName();
        $this->methodName = $methodName;

        // if method exists, return
        if (method_exists($this, $methodName)) {
            return $this->$methodName($arguments);
        }

        if (in_array(Str::snake($methodName), $this->getAllowedMethods())) {
            $this->setRouteFromConfig($fqcn, $methodName);

            if (count($arguments) > 0) {
                $this->setPayload($arguments);
            }

            return $this->execute();
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            $fqcn,
            $methodName
        ));
    }

    private function setRouteFromConfig(string $fqcn, string $method): void
    {
        $route_prefix = str($fqcn)->afterLast('\\')->remove('Service')->lower()->value;
        $route_name = str($method)->snake()->value;

        $route = config('lazada.routes.' . $route_prefix . '.' . $route_name);

        $split = str($route)->explode(' ');

        if (count($split) == 2) {
            $this->setMethod(data_get($split, '0'));
            $this->setRoute(data_get($split, '1'));
        } elseif (count($split) == 1) {
            $this->setRoute(data_get($split, '0'));
        }
    }

    protected function execute()
    {
        $method = $this->getMethod();
        $url = $this->getUrl();

        $response = Http::asJson();

        $payload = $this->getPayload();

        $commonParameters = $this->getCommonParameters();

        $payload = array_merge($commonParameters, $payload);

        $signature = $this->lazada->getSignature($this->getRoute(), $payload);

        throw_if(!$signature, LogicException::class, __('Failed to generate signature.'));

        $payload = array_merge($payload, ['sign' => $signature]);

        $request = LazadaMessage::create([
            'action' => $this->serviceName . '::' . $this->methodName,
            'url' => $url,
            'request' => $payload,
        ]);

        $response = $response->$method($url, $payload);

        $response->throw(function (Response $response, RequestException $e) use ($request) {
            $request->update([
                'error' =>  Str::limit(trim($e->getMessage()), 255),
            ]);
        });

        $result = $response->json();

        if ($response->successful()) {
            $code = data_get($result, 'code');

            $request->update([
                'response' => $result,
                'request_id' => data_get($result, 'request_id'),
                'error' => $code != '0' ? (data_get($result, 'message') ?? data_get($result, 'code')) : null
            ]);

            // success
            if ($code == '0') {

                $this->afterRequest($request, $result);

                return $result;
            }

            // http success but api request failed
            throw new LazadaAPIError($result ?? ['code' => __('Error')]);
        }

        $request->update([
            'error' =>  __('API Server Error'),
        ]);

        throw new LazadaAPIError(['code' => __('Error'), 'message' => __('API Server Error')]);
    }

    private function afterRequest(LazadaMessage $request, array $result = []): void
    {
        $methodName = 'after' . Str::studly($this->methodName) . 'Request';

        if (method_exists($this, $methodName)) {
            $this->$methodName($request, $result);
        }
    }

    public function getCommonParameters(): array
    {
        $params = [
            'app_key' => $this->lazada->getAppKey(),
            'timestamp' => now()->getTimestampMs(),
            'sign_method' => $this->lazada->getSignMethod(),
        ];

        if (!($this instanceof \Laraditz\Lazada\Services\AuthService)) {
            $seller = LazadaSeller::where('short_code', $this->lazada->getSellerId())->firstOrFail();

            $params['access_token'] = $seller->accessToken?->access_token;
        }

        return $params;
    }

    protected function getAllowedMethods(): array
    {
        $route_prefix = str($this->serviceName)->remove('Service')->lower()->value;

        return array_keys(config('lazada.routes.' . $route_prefix) ?? []);
    }

    protected function getUrl(): string
    {
        $region = $this->lazada->getRegion();

        if (
            $this instanceof \Laraditz\Lazada\Services\AuthService
            && in_array($this->methodName, ['accessToken', 'refreshToken'])
        ) {
            $base_url = config('lazada.auth_url');
        } else {
            $base_url = config('lazada.base_url.' . $region);
        }

        return $base_url . $this->getRoute();
    }

    protected function route(string $route): self
    {
        $this->setRoute($route);

        return $this;
    }

    protected function setRoute(string $route): void
    {
        $this->route = $route;
    }

    protected function getRoute(): string
    {
        return $this->route;
    }

    protected function method(string $method): self
    {
        $this->setMethod($method);

        return $this;
    }

    protected function setMethod(string $method): void
    {
        if ($method) {
            $this->method = strtolower($method);
        }
    }

    protected function getMethod(): string
    {
        return $this->method;
    }

    public function payload(array $payload): self
    {
        $this->setPayload($payload);

        return $this;
    }

    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    protected function getPayload(): array
    {
        return $this->payload;
    }

    public function queryString(array $queryString): self
    {
        $this->setQueryString($queryString);

        return $this;
    }

    protected function setQueryString(array $queryString): void
    {
        $this->queryString = $queryString;
    }

    protected function getQueryString(): array
    {
        return $this->queryString;
    }

    public function sellerId(string $sellerId): self
    {
        $this->setSellerId($sellerId);

        return $this;
    }

    protected function setSellerId(string $sellerId): void
    {
        $this->sellerId = $sellerId;
    }

    protected function getSellerId(): string
    {
        return $this->sellerId;
    }
}

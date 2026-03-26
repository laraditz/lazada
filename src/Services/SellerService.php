<?php

namespace Laraditz\Lazada\Services;

use Laraditz\Lazada\Enums\ActiveStatus;
use Laraditz\Lazada\Enums\Affirmative;
use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Models\LazadaSeller;
use Illuminate\Support\Arr;

class SellerService extends BaseService
{
    public function afterGetRequest(LazadaMessage $request, array $result = []): void
    {
        $data = data_get($result, 'data');
        $seller_id = data_get($data, 'seller_id');
        $company_name = data_get($data, 'name_company');
        $verified = data_get($data, 'verified');
        $cross_border = data_get($data, 'cb');
        $status = $this->getSellerStatus(data_get($data, 'status'));


        if ($seller_id) {
            LazadaSeller::updateOrCreate([
                'id' => $seller_id
            ], [
                ...Arr::only($data, ['name', 'logo_url', 'location', 'email', 'short_code']),
                'company_name' => $company_name,
                'verified' => $verified === true || $verified === 'true' ? Affirmative::Yes : Affirmative::No,
                'cross_border' => $cross_border === true || $cross_border === 'true' ? Affirmative::Yes : Affirmative::No,
                'status' => $status,
            ]);
        }
    }

    public function info()
    {
        return $this->lazada->seller;
    }

    private function getSellerStatus(?string $status): ActiveStatus
    {
        $status = strtoupper($status);

        return match ($status) {
            'ACTIVE' => ActiveStatus::Active,
            'INACTIVE' => ActiveStatus::Inactive,
            'DELETED' => ActiveStatus::Deleted,
            default => ActiveStatus::Others,
        };
    }
}

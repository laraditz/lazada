<?php

namespace Laraditz\Lazada\Services;

use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Models\LazadaFinanceDetail;

class FinanceService extends BaseService
{
    public function afterTransactionDetailRequest(LazadaMessage $request, array $result = []): void
    {
        $data = data_get($result, 'data');

        if ($data && count($data) > 0) {

            $collect = collect($data)->mapToGroups(function ($item, $key) {
                return [data_get($item, 'order_no') => $item];
            });

            $collect->each(function ($items, $orderNo) {
                if ($items->isNotEmpty()) {
                    LazadaFinanceDetail::updateOrCreate(
                        [
                            'order_id' => $orderNo,
                        ],
                        [
                            'data' => $items->toArray(),
                        ]
                    );
                }
            });
        }
    }
}

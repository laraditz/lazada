<?php

namespace Laraditz\Lazada\Services;

use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Models\LazadaOrder;

class OrderService extends BaseService
{
    public function afterListRequest(LazadaMessage $request, array $result = []): void
    {
        $orders = data_get($result, 'data.orders');

        if ($orders && count($orders) > 0) {

            foreach ($orders as $order) {

                $order_id = data_get($order, 'order_id');

                if ($order_id) {
                    LazadaOrder::updateOrCreate([
                        'id' => $order_id,
                    ], [
                        'seller_id' => $request->seller_id,
                        'statuses' => data_get($order, 'statuses'),
                    ]);
                }
            }
        }
    }

    public function afterGetRequest(LazadaMessage $request, array $result = []): void
    {
        $data = data_get($result, 'data');
        $order_id = data_get($data, 'order_id');

        if ($order_id) {
            LazadaOrder::updateOrCreate([
                'id' => $order_id,
            ], [
                'seller_id' => $request->seller_id,
                'statuses' => data_get($data, 'statuses'),
            ]);
        }
    }
}

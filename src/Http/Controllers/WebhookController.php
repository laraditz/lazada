<?php

namespace Laraditz\Lazada\Http\Controllers;

use Illuminate\Http\Request;
use Laraditz\Lazada\Enums\WebPushType;
use Laraditz\Lazada\Events\WebPushReceived;
use Laraditz\Lazada\Exceptions\LazadaException;
use Laraditz\Lazada\Models\LazadaMessage;
use Laraditz\Lazada\Models\LazadaOrder;
use Laraditz\Lazada\Models\LazadaReverseOrder;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();
        $signature = $request->header('Authorization');

        throw_if(!$signature, LazadaException::class, __('Missing Signature.'));
        throw_if(!$data, LazadaException::class, __('Missing payload.'));

        if (config('lazada.log_message') === true) {
            logger()->info('Lazada web push : Signature ' . $signature);
            logger()->info('Lazada web push : Received', $data);
        }

        $match_signature = app('lazada')->getWebPushSignature(json_encode($data));

        throw_if(strtoupper($signature) !== $match_signature, LazadaException::class, __('Signature not matched.'));

        if ($data) {

            event(new WebPushReceived($data));

            try {
                LazadaMessage::create([
                    'action' => 'webhook',
                    'response' => $data,
                ]);

                $message_type = data_get($data, 'message_type') ?? data_get($data, 'msg_type');
                $web_push_type = WebPushType::tryFrom($message_type);

                if ($web_push_type === WebPushType::TradeOrder) {
                    $this->createOrUpdateOrder($data);
                } elseif ($web_push_type === WebPushType::ReverseOrder) {
                    $this->createOrUpdateReverseOrder($data);
                }
            } catch (\Throwable $th) {
                logger()->error('Lazada web push : ' . $th->getMessage(), $data);
                //throw $th;
            }
        }
    }

    private function createOrUpdateOrder(array $data): void
    {
        $seller_id = data_get($data, 'seller_id');
        $order_id = data_get($data, 'data.trade_order_id');
        $reverse_order_id = data_get($data, 'data.reverse_order_id'); // for return or refund case
        $status = data_get($data, 'data.order_status');

        if ($order_id && $status && is_string($status)) {
            LazadaOrder::updateOrCreate([
                'id' => $order_id
            ], [
                'seller_id' => $seller_id,
                'status' => $status,
            ]);
        }

        if ($reverse_order_id) {
            LazadaReverseOrder::updateOrCreate([
                'id' => $order_id
            ], []);
        }
    }

    private function createOrUpdateReverseOrder(array $data): void
    {
        $order_id = data_get($data, 'data.trade_order_id');
        $reverse_order_id = data_get($data, 'data.reverse_order_id');
        $seller_id = data_get($data, 'seller_id');
        $buyer_id = data_get($data, 'data.buyer_id');
        $status = data_get($data, 'data.reverse_status');

        if ($reverse_order_id && $status && is_string($status)) {
            LazadaReverseOrder::updateOrCreate([
                'id' => $reverse_order_id
            ], [
                'order_id' => $order_id,
                'seller_id' => $seller_id,
                'buyer_id' => $buyer_id,
                'status' => $status,
            ]);
        }
    }
}

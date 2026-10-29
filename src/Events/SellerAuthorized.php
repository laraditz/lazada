<?php

namespace Laraditz\Lazada\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laraditz\Lazada\Models\LazadaAccessToken;
use Laraditz\Lazada\Models\LazadaSeller;

class SellerAuthorized
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly LazadaSeller $seller,
        public readonly LazadaAccessToken $accessToken,
    ) {
    }
}

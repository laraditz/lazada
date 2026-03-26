<?php

namespace Laraditz\Lazada\Tests;

use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaSeller;
use Laraditz\Lazada\Services\SellerService;

class SellerServiceTest extends TestCase
{
    public static $latestResponse;

    public function test_info_returns_pre_resolved_seller(): void
    {
        $seller = LazadaSeller::factory()->create(['short_code' => 'TESTSHOP']);

        $lazada = new Lazada(seller_short_code: 'TESTSHOP');
        $lazada->checkSeller();

        $service = new SellerService(lazada: $lazada);
        $result = $service->info();

        $this->assertSame($seller->id, $result->id);
        $this->assertSame('TESTSHOP', $result->short_code);
    }
}

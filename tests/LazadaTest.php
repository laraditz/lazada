<?php

namespace Laraditz\Lazada\Tests;

use Laraditz\Lazada\Lazada;
use Laraditz\Lazada\Models\LazadaSeller;
use LogicException;

class LazadaTest extends TestCase
{
    private static $latestResponse;

    // --- checkSeller() resolution ---

    public function test_resolves_seller_by_numeric_id(): void
    {
        $seller = LazadaSeller::factory()->create(['id' => 99901, 'short_code' => 'NUMSHOP']);

        $lazada = new Lazada(seller_short_code: '99901');
        $lazada->checkSeller();

        $this->assertSame(99901, $lazada->seller->id);
    }

    public function test_resolves_seller_by_short_code(): void
    {
        $seller = LazadaSeller::factory()->create(['short_code' => 'MYSHOP01']);

        $lazada = new Lazada(seller_short_code: 'MYSHOP01');
        $lazada->checkSeller();

        $this->assertSame($seller->id, $lazada->seller->id);
    }

    public function test_falls_back_to_short_code_when_numeric_id_not_found(): void
    {
        // short_code is numeric string but no seller has that numeric id
        $seller = LazadaSeller::factory()->create(['short_code' => '77777']);

        $lazada = new Lazada(seller_short_code: '77777');
        $lazada->checkSeller();

        $this->assertSame($seller->id, $lazada->seller->id);
    }

    public function test_throws_missing_when_no_seller_id(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Missing Seller ID.');

        config(['lazada.seller_short_code' => null]); // clear config default too
        $lazada = new Lazada(seller_short_code: null);
        $lazada->checkSeller();
    }

    public function test_throws_invalid_when_seller_not_found(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid Seller ID.');

        $lazada = new Lazada(seller_short_code: 'NONEXISTENT');
        $lazada->checkSeller();
    }

    public function test_check_seller_is_idempotent(): void
    {
        LazadaSeller::factory()->create(['short_code' => 'IDEMPSHOP']);

        $lazada = new Lazada(seller_short_code: 'IDEMPSHOP');
        $lazada->checkSeller();
        $first = $lazada->seller;

        $lazada->checkSeller(); // second call — must not re-query

        $this->assertSame($first, $lazada->seller); // same object reference
    }

    // --- Config default fallback ---

    public function test_constructor_falls_back_to_config_default(): void
    {
        // TestCase sets lazada.seller_short_code = 'TESTSHOP'
        LazadaSeller::factory()->create(['short_code' => 'TESTSHOP']);

        $lazada = new Lazada(); // no seller_short_code argument
        $lazada->checkSeller();

        $this->assertSame('TESTSHOP', $lazada->seller->short_code);
    }

    // --- Per-call seller_id override ---

    public function test_setting_new_short_code_and_resetting_seller_re_resolves(): void
    {
        $sellerA = LazadaSeller::factory()->create(['short_code' => 'SHOPA']);
        $sellerB = LazadaSeller::factory()->create(['short_code' => 'SHOPB']);

        $lazada = new Lazada(seller_short_code: 'SHOPA');
        $lazada->checkSeller();
        $this->assertSame($sellerA->id, $lazada->seller->id);

        // Simulate what __call() does on per-call override:
        $lazada->setSellerShortCode('SHOPB');
        $lazada->seller = null; // reset forces re-resolution
        $lazada->checkSeller();

        $this->assertSame($sellerB->id, $lazada->seller->id);
    }

    // --- make() ---

    public function test_make_accepts_seller_id_and_maps_to_seller_short_code(): void
    {
        LazadaSeller::factory()->create(['short_code' => 'MKSHOP']);

        $lazada = Lazada::make(seller_id: 'MKSHOP');

        // The public seller_short_code getter should reflect the mapped value
        $this->assertSame('MKSHOP', $lazada->getSellerShortCode());

        // And it should resolve correctly
        $lazada->checkSeller();
        $this->assertSame('MKSHOP', $lazada->seller->short_code);
    }
}

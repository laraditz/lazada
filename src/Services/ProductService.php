<?php

namespace Laraditz\Lazada\Services;

use Illuminate\Support\Facades\DB;
use Laraditz\Lazada\Models\LazadaProduct;
use Laraditz\Lazada\Models\LazadaProductSku;
use Laraditz\Lazada\Models\LazadaMessage;

class ProductService extends BaseService
{
    public function afterListRequest(LazadaMessage $request, array $result = []): void
    {
        $code     = data_get($result, 'code');
        $products = data_get($result, 'data.products');
        $sellerId = $request->seller_id;

        if ($code === '0' && is_array($products)) {
            foreach ($products as $product) {
                $this->syncProduct($sellerId, $product);
            }
        }
    }

    public function afterGetRequest(LazadaMessage $request, array $result = []): void
    {
        $code     = data_get($result, 'code');
        $products = data_get($result, 'data.products');
        $sellerId = $request->seller_id;

        if ($code === '0' && is_array($products)) {
            foreach ($products as $product) {
                $this->syncProduct($sellerId, $product);
            }
        }
    }

    private function syncProduct(int|string $sellerId, array $productData): void
    {
        $itemId = data_get($productData, 'item_id');
        $skus   = data_get($productData, 'skus');

        if (!$itemId) {
            return;
        }

        DB::transaction(function () use ($sellerId, $itemId, $productData, $skus) {

            $product = LazadaProduct::updateOrCreate(
                [
                    'seller_id' => $sellerId,
                    'id'  => $itemId,
                ],
                [
                    'name'             => data_get($productData, 'attributes.name'),
                    'brand'            => data_get($productData, 'attributes.brand'),
                    'model'            => data_get($productData, 'attributes.model'),
                    'status'           => data_get($productData, 'status'),
                    'primary_category' => data_get($productData, 'primary_category'),
                    'description'      => data_get($productData, 'attributes.description'),
                    'attributes'       => data_get($productData, 'attributes'),
                ]
            );

            if (is_array($skus) && count($skus) > 0) {
                $this->syncProductSkus($product, $skus);
            }
        });
    }

    private function syncProductSkus(LazadaProduct $product, array $skus): void
    {
        $existingSkuIds = [];

        foreach ($skus as $sku) {

            $skuId = data_get($sku, 'SkuId');

            if (!$skuId) {
                continue;
            }

            $existingSkuIds[] = $skuId;

            LazadaProductSku::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'id' => $skuId,
                ],
                [
                    'seller_sku' => data_get($sku, 'SellerSku'),
                    'shop_sku'   => data_get($sku, 'ShopSku'),
                    'status'     => data_get($sku, 'Status'),
                    'price'      => data_get($sku, 'price'),
                    'quantity'   => data_get($sku, 'quantity'),
                    'available'  => data_get($sku, 'Available'),
                    'variation'  => data_get($sku, 'Variation'),
                    'color_family' => data_get($sku, 'color_family'),
                    'images'       => data_get($sku, 'Images'),
                    'multi_warehouse_inventories' => data_get($sku, 'multiWarehouseInventories'),
                    'sale_prop'    => data_get($sku, 'saleProp'),
                    'package_width'  => data_get($sku, 'package_width'),
                    'package_height' => data_get($sku, 'package_height'),
                    'package_length' => data_get($sku, 'package_length'),
                    'package_weight' => data_get($sku, 'package_weight'),
                ]
            );
        }

        $product->skus()
            ->whereNotIn('id', $existingSkuIds)
            ->delete();
    }
}
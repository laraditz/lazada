<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lazada_product_skus', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');

            $table->string('seller_sku')->nullable();
            $table->string('shop_sku')->nullable();

            $table->string('status')->nullable();
            $table->integer('price')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('available')->nullable();

            $table->string('variation')->nullable();
            $table->string('color_family')->nullable();

            $table->json('images')->nullable();
            $table->json('multi_warehouse_inventories')->nullable();
            $table->json('sale_prop')->nullable();

            $table->integer('package_width')->nullable();
            $table->integer('package_height')->nullable();
            $table->integer('package_length')->nullable();
            $table->integer('package_weight')->nullable();

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('lazada_products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lazada_product_skus');
    }
};
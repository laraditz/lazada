<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lazada_products', function (Blueprint $table) {
            $table->id();
            $table->string('seller_id')->index();
            $table->string('name')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('primary_category')->nullable();
            $table->longText('description')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lazada_products');
    }
};
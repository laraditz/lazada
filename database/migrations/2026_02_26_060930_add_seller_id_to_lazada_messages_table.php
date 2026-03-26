<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lazada_messages', function (Blueprint $table) {
             $table->string('seller_id')
                ->nullable()
                ->after('id')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lazada_messages', function (Blueprint $table) {
            $table->dropColumn('seller_id');
        });
    }
};
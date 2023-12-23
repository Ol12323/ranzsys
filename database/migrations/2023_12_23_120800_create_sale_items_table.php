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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_transaction_id');
            $table->unsignedBigInteger('service_id');
            $table->string('service_name');
            $table->decimal('service_price', 8, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 8, 2);
            $table->timestamps();

            $table->foreign('service_id')->references('service_id')->on('services');
            $table->foreign('sale_transaction_id')->references('id')->on('sale_transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

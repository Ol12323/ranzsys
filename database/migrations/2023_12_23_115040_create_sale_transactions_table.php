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
        Schema::create('sale_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sales_name');
            $table->string('process_type');
            $table->unsignedBigInteger('customer_id');
            $table->decimal('customer_cash_change', 8, 2);
            $table->decimal('total_amount', 8, 2);
            $table->unsignedBigInteger('processed_by');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('processed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_transactions');
    }
};

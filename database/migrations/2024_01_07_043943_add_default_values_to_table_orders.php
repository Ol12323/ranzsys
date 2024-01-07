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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('Pending')->change();
            $table->string('total_amount')->default(0.00)->change();
            $table->string('payment_due')->default(0.00)->change();
            $table->text('receipt_screenshot')->nullable()->change();
            $table->date('service_date')->nullable()->change();
            $table->unsignedBigInteger('time_slot_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};

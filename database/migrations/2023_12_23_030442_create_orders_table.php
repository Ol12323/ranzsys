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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_name');
            $table->string('service_type');
            $table->string('status');
            $table->decimal('total_amount', 8, 2);
            $table->decimal('payment_due', 8, 2);
            $table->string('mode_of_payment');
            $table->text('receipt_screenshot');
            $table->date('service_date');
            $table->unsignedBigInteger('time_slot_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('time_slot_id')->references('id')->on('time_slots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

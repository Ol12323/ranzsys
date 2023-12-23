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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('price', 8, 2);
            $tbale->integer('quantity');
            $table->decimal('sub_total', 8, 2);
            $table->string('mode_of_payment');
            $table->date('appointment_date');
            $table->text('payment_receipt');
            $table->unsignedBigInteger('time_slot_id');
            $table->string('design_type');
            $table->text('design_description');
            $table->text('design_file_path');
            $table->timestamps();

            $table->foreign('service_id')->references('service_id')->on('services');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};

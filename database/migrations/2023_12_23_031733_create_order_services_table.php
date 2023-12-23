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
        Schema::create('order_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('price', 8, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 8, 2);
            $table->string('design_type');
            $table->text('design_description');
            $table->text('design_file_path');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('service_id')->references('service_id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};

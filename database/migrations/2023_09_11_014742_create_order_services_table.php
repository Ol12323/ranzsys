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
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->string('service_name');
            $table->decimal('price', 10, 2); // Price per unit of service
            $table->integer('quantity'); // Quantity of services ordered
            $table->decimal('subtotal', 10, 2); // Subtotal for the service
            $table->string('file_path'); // Column for the primary file
            $table->json('alternative_files')->nullable(); // JSON column for alternative files
            $table->timestamps();
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

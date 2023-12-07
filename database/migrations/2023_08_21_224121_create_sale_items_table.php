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
            $table->decimal('service_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();

            // Define foreign key relationship
            $table->foreign('sale_transaction_id')
                ->references('id')
                ->on('sale_transactions')
                ->onDelete('cascade'); // or any other appropriate action
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade'); // or any other appropriate action
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

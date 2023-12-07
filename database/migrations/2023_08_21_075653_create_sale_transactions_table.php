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
            $table->id(); // Creates an auto-incrementing primary key field
            $table->string('sales_name'); // Column to store the sales name as a string
            $table->string('process_type');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users'); // Assuming you have a customers table
            $table->decimal('customer_cash_change', 10, 2); // Change given to the customer
            $table->decimal('total_amount', 10, 2);
            $table->unsignedBigInteger('processed_by')->nullable(); // Staff or admin who processed the transaction
            $table->foreign('processed_by')->references('id')->on('users'); // Replace with the actual staff or admin table name
            $table->timestamps(); // Creates created_at and updated_at timestamps
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

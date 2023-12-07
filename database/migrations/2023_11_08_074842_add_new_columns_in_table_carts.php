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
        Schema::table('carts', function (Blueprint $table) {
            $table->string('mode_of_payment')->default('Not applicable')->after('sub_total');
            $table->date('appointment_date')->nullable()->after('mode_of_payment');
            $table->text('payment_receipt')->after('appointment_date');
            $table->unsignedBigInteger('time_slot_id')->nullable()->constrained('time_slots')->after('payment_receipt');
            $table->string('design_type')->default('Not applicable')->after('time_slot_id');
            $table->text('description')->default('Not applicable')->after('design_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            //
        });
    }
};

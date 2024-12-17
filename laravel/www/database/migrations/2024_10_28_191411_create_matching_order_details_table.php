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
        Schema::create('matching_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('matching_orders')->onDelete('cascade');
            $table->foreignId('cast_id')->constrained('casts');
            $table->unsignedInteger('order_acception_status')->constrained('statuses')->default(703);
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->unsignedInteger('applied_point');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_order_details');
    }
};

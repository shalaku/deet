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
        Schema::create('matching_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->references('id')->on('customers');
            $table->enum('order_type', ['direct', 'request']);
            $table->unsignedInteger('status')->constrained('statuses')->default(0);
            $table->dateTime('planned_meeting_date_time')->nullable();
            $table->time('planned_meeting_time')->nullable();
            $table->string('place_post_code', 7)->nullable();
            $table->string('place_prefecture', 32)->nullable();
            $table->text('place_municipalitie')->nullable();
            $table->text('place_street')->nullable();
            $table->text('place_building')->nullable();
            $table->string('place_desc')->nullable();
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_orders');
    }
};

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
        Schema::create('cast_bank_acc_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cast_id')->references('id')->on('casts');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('branch')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cast_bank_acc_details');
    }
};

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
        Schema::create('customer_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_lists')->onDelete('cascade');
            $table->foreignId('image_id')->references('id')->on('image_infos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_images');
    }
};

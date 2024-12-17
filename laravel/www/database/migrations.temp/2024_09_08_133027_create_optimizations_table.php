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
        Schema::create('optimizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_id')->references('id')->on('image_infos')->cascadeOnDelete();
            $table->enum('webp_conversion', ['failed', 'done', 'pending', 'in_progress'])->default('pending');
            $table->double('original_size', 15, 2);
            $table->double('optimized_size', 15, 2)->nullable();
            $table->double('webp_size', 15, 2)->nullable();
            $table->enum('optimization_status', ['failed', 'done', 'pending', 'in_progress'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimizations');
    }
};

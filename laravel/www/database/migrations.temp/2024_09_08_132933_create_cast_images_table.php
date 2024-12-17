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
        Schema::create('cast_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cast_id')->constrained('cast_lists')->cascadeOnDelete();
            $table->foreignId('image_id')->constrained('image_infos')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cast_images');
    }
};

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
        Schema::create('site_admin_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_admin_id')->references('id')->on('user_lists')->cascadeOnDelete();
            $table->foreignId('image_id')->references('id')->on('image_infos')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_admin_images');
    }
};

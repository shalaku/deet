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
        Schema::create('image_infos', function (Blueprint $table) {
            $table->id();
            $table->string('public_url');
            $table->string('storage_url');
            $table->string('title');
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->string('hash')->nullable();
            $table->enum('file_type', ['general', 'featured'])->default('general');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_info');
    }
};

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
        Schema::create('request_matching_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matching_id')->constrained('request_matchings');
            $table->foreignId('cast_id')->constrained('casts');
            $table->unsignedInteger('status')->default(510)->constrained('statuses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_matching_details');
    }
};

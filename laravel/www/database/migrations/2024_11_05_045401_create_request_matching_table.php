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
        Schema::create('request_matchings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->unsignedInteger('status')->default(510)->constrained('statuses');
            $table->string('municipalities', 64)->nullable();
            $table->string('area_name', 64)->nullable();
            $table->unsignedTinyInteger('number_of_people');
            $table->dateTime('requested_start_time')->nullable();
            $table->time('requested_matching_term')->nullable();
            $table->string('cast_birthplace', 32)->nullable();
            $table->unsignedTinyInteger('cast_age_min')->default(0);
            $table->unsignedTinyInteger('cast_age_max')->default(99);
            $table->unsignedSmallInteger('cast_height_min')->default(0);
            $table->unsignedSmallInteger('cast_height_max')->default(999);
            $table->json('cast_tag')->nullable();
            $table->json('cast_rank')->nullable();
            $table->unsignedMediumInteger('mid_night_fee')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_matchings');
    }
};

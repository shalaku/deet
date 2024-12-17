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
        Schema::create('casts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name_ja', 64);
            $table->string('name_kana', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->foreignId('introducer_id')->nullable()->references('id')->on('casts')->onDelete('cascade');
            $table->foreignId('person_in_charge_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('registered_date')->nullable();
            $table->foreignId('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->string('rank');
            $table->string('post_code', 7)->nullable();
            $table->string('prefecture', 32)->nullable();
            $table->string('municipalitie')->nullable();
            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('station')->nullable();
            $table->enum('footwork', ['good', 'average', 'bad']);
            $table->enum('alcohol', ['good', 'average', 'bad', 'not']);
            $table->string('day_work')->nullable();
            $table->string('night_work')->nullable();
            $table->integer('height', false, true)->nullable();
            $table->integer('three_size_b', false, true)->nullable();
            $table->integer('three_size_w', false, true)->nullable();
            $table->integer('three_size_h', false, true)->nullable();
            $table->enum('vip_status', ['good', 'average', 'not'])->nullable();
            $table->timestamp('birthday')->nullable();
            $table->boolean('ceo_check')->nullable();
            $table->string('instagram_id', 64)->nullable();
            $table->foreignId('category_id')->nullable()->references('id')->on('cast_categories')->onDelete('cascade');
            $table->json('tag_of_spec')->nullable();
            $table->text('notices')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casts');
    }
};

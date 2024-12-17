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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->references('id')->on('statuses')->onDelete('cascade');
            $table->string('name_ja', 64);
            $table->string('name_kana', 64)->nullable();
            $table->timestamp('birthday')->nullable();
            $table->foreignId('introducer_id')->nullable()->references('id')->on('casts')->onDelete('cascade');
            $table->foreignId('person_in_charge_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->references('id')->on('customer_category_lists')->onDelete('cascade');
            $table->timestamp('registered_date')->nullable();
            $table->json('tag_of_preference')->nullable();
            $table->text('notices')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

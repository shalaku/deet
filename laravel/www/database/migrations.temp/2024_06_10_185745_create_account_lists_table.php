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
        Schema::create('account_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name_ja');
            $table->string('chef_admin')->nullable();
            $table->string('post_code')->nullable();
            $table->string('prefectures')->nullable();
            $table->string('municipalities')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_lists');
    }
};

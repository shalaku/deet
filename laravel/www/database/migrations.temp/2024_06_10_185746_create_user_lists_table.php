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
        Schema::create('user_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name_ja');
            $table->string('email')->unique();
            $table->string('password');
            $table->set('account_type', ['admin', 'store', 'cast', 'customer']);
            $table->set('user_type', ['-', 'admin', 'staff']);
            $table->foreignId('account_id')->nullable()->references('id')->on('account_lists')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_lists');
    }
};

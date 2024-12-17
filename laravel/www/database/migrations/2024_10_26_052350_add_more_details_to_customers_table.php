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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->references('id')->on('users')->onDelete('cascade');
            $table->string('height')->nullable()->after('notices');
            $table->string('municipalitie')->nullable()->after('height');
            $table->string('street')->nullable()->after('municipalitie');
            $table->string('building')->nullable()->after('street');
            $table->string('type')->nullable()->after('building');
            $table->string('work')->nullable()->after('type');
            $table->string('hair')->nullable()->after('work');
            $table->string('hair_style')->nullable()->after('hair');
            $table->boolean('alcohol')->nullable()->after('hair_style');
            $table->string('education')->nullable()->after('alcohol');
            $table->boolean('tobacco')->nullable()->after('education');
            $table->integer('siblings')->nullable()->after('tobacco');
            $table->string('cohabitant')->nullable()->after('siblings');
            $table->json('fav_cast_ids')->nullable()->after('cohabitant');
            $table->integer('points_holded')->default(0)->after('fav_cast_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'height',
                'municipalitie',
                'street',
                'building',
                'type',
                'work',
                'hair',
                'hair_style',
                'alcohol',
                'education',
                'tobacco',
                'siblings',
                'cohabitant',
                'fav_cast_ids',
                'points_holded'
            ]);
        });
    }
};

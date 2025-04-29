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
        Schema::table('craftsman_gallery', function (Blueprint $table) {
            $table->string('img_path')->nullable()->change();
            $table->string('img_title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('craftsman_gallery', function (Blueprint $table) {
            $table->string('img_path')->nullable(false)->change();
            $table->string('img_title')->nullable(false)->change();
        });
    }
};

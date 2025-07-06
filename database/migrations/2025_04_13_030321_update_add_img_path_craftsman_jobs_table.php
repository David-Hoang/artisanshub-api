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
        Schema::table('craftsman_jobs', function (Blueprint $table) {
            $table->string('img_path')->nullable()->after('name');
            $table->string('img_title')->nullable()->after('img_path');
            $table->longText('description')->nullable()->after('img_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('craftsman_jobs', function (Blueprint $table) {
            $table->dropColumn('img_path');
            $table->dropColumn('img_title');
            $table->dropColumn('description');
        });
    }
};

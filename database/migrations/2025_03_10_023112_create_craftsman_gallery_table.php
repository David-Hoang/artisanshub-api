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
        Schema::create('craftsman_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craftsman_id')->constrained('craftsmen')->cascadeOnDelete();
            $table->string('img_path');
            $table->string('img_title');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('craftsman_gallery');
    }
};

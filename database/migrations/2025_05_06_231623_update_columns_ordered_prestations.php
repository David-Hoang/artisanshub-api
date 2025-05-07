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
        Schema::table('ordered_prestations', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->longText('description')->change();
            $table->date('date')->nullable()->change();
        });

        Schema::table('ordered_prestations', function (Blueprint $table) {
            $table->enum('state', [
                'await-craftsman',
                'await-client',
                'refused-by-client',
                'refused-by-craftsman',
                'confirmed',
                'completed'
            ])->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordered_prestations', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->decimal('price', 10, 2)->change();
            $table->longText('description')->nullable()->change();
            $table->date('date')->change();
        });
    
        Schema::table('ordered_prestations', function (Blueprint $table) {
            $table->enum('state', [
                'cancelled',
                'pending',
                'in-progress',
                'complete'
            ])->after('date');
        });
    }
};

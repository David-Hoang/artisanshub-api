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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->nullable();
            $table->string('phone');
            $table->enum('role', [
                'admin',
                'craftsman',
                'client'
            ]);
            $table->string('city');
            $table->enum('region', [
                "Auvergne-Rhône-Alpes",
                "Bourgogne-Franche-Comté",
                "Bretagne",
                "Centre-Val de Loire",
                "Corse",
                "Grand Est",
                "Hauts-de-France",
                "Île-de-France",
                "Normandie",
                "Nouvelle-Aquitaine",
                "Occitanie",
                "Pays de la Loire",
                "Provence-Alpes-Côte d\'Azur",
                "Guadeloupe",
                "Guyane",
                "Martinique",
                "Mayotte",
                "La Réunion"
            ]);
            $table->string('zipcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('username');
            $table->dropColumn('phone');
            $table->dropColumn('role');
            $table->dropColumn('city');
            $table->dropColumn('region');
            $table->dropColumn('zipcode');
        });
    }
};

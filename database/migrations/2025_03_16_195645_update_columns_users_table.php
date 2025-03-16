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
            $table->string('last_name')->after('id');
            $table->string('first_name')->after('last_name');
            $table->string('username')->nullable()->after('email_verified_at');
            $table->enum('role', [
                'admin',
                'craftsman',
                'client'
            ])->after('username');
            $table->string('phone')->after('password');
            $table->string('city')->after('phone');
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
            ])->after('city');
            $table->string('zipcode')->after('region');
            $table->dropColumn('name');
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
            $table->string('name')->nullable();
        });
    }
};

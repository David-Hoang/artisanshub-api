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
        Schema::table('messages', function (Blueprint $table) {
            // Drop old constrains
            $table->dropForeign(['receiver_id']);
            $table->dropColumn('receiver_id');

            $table->dropForeign(['sender_id']);
            $table->dropColumn('sender_id');
        });

        Schema::table('messages', function (Blueprint $table) {
            // recreate constrains with cascadeondelete
            $table->foreignId('receiver_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->after('id');

            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->after('receiver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['receiver_id']);
            $table->dropForeign(['sender_id']);

            $table->dropColumn('receiver_id');
            $table->dropColumn('sender_id');

        });

        Schema::table('messages', function (Blueprint $table) {

            $table->foreignId('receiver_id')->constrained('users')->after('id');
            $table->foreignId('sender_id')->constrained('users')->after('receiver_id');
        });
    }
};
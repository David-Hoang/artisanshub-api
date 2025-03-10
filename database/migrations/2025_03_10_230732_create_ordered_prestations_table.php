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
        Schema::create('ordered_prestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('craftsman_id')->nullable()->constrained('craftsmen')->nullOnDelete();
            $table->decimal('price', 10, 2);
            $table->longText('description')->nullable();
            $table->date('date');
            $table->enum('state', [
                'cancelled',
                'pending',
                'in-progress',
                'complete'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordered_prestations');
    }
};

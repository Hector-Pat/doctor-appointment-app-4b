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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // IMPORTANTE: tu columna es speciality_id
            $table->foreignId('speciality_id')
                ->nullable()
                ->constrained('specialities')
                ->nullOnDelete();

            // IMPORTANTE: tu columna se llama medical_license_number
            $table->string('medical_license_number')->nullable();

            $table->text('biography')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

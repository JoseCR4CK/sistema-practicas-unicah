<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('company_responsible_id')
                ->nullable()
                ->constrained('company_responsibles')
                ->nullOnDelete();

            $table->foreignId('teacher_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('academic_period');

            $table->unsignedSmallInteger('academic_year');

            $table->string('stage')
                ->default('Solicitud');

            $table->string('status')
                ->default('ACTIVE');

            $table->unsignedTinyInteger('progress')
                ->default(0);

            $table->date('proposed_start_date')
                ->nullable();

            $table->date('start_date')
                ->nullable();

            $table->date('end_date')
                ->nullable();

            $table->unsignedInteger('required_hours')
                ->nullable();

            $table->unsignedInteger('completed_hours')
                ->default(0);

            $table->timestamps();

            $table->index([
                'academic_year',
                'academic_period'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
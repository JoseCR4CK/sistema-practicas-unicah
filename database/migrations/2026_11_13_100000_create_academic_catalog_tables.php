<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_careers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable()->unique();
            $table->string('status', 40)->default('ACTIVE');
            $table->text('source')->nullable();
            $table->timestamps();
        });
        Schema::create('academic_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_career_id')->constrained()->restrictOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->decimal('credits', 6, 2)->nullable();
            $table->decimal('weekly_class_hours', 6, 2)->nullable();
            $table->string('status', 40)->default('ACTIVE');
            $table->text('source')->nullable();
            $table->timestamps();
            $table->unique(['academic_career_id', 'code']);
            $table->index(['academic_career_id', 'status']);
        });
        Schema::table('academic_loads', function (Blueprint $table) {
            $table->foreignId('academic_career_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('academic_subject_id')->nullable()->constrained()->restrictOnDelete();
        });
    }
};

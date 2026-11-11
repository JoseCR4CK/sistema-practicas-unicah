<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->string('academic_period', 80);
            $table->unsignedSmallInteger('academic_year');
            $table->string('subject');
            $table->string('career')->nullable();
            $table->string('section', 80)->nullable();
            $table->json('weekdays');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->date('valid_from');
            $table->date('valid_until');
            $table->decimal('credits', 6, 2)->nullable();
            $table->decimal('weekly_class_hours', 6, 2)->nullable();
            $table->string('status', 40)->default('ACTIVE');
            $table->uuid('submission_key')->unique();
            $table->unsignedInteger('revision')->default(0);
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['teacher_id', 'academic_year', 'academic_period']);
            $table->index(['teacher_id', 'status', 'valid_from', 'valid_until']);
        });
        Schema::create('academic_load_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_load_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->text('reason')->nullable();
            $table->json('before');
            $table->json('after');
            $table->timestamps();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_committee_id')->unique()->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->string('status')->default('Pendiente');
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('performed_at')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
        });
        Schema::create('defense_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_id')->constrained()->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedInteger('committee_revision');
            $table->foreignId('proposed_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::table('defenses', function (Blueprint $table) {
            $table->foreign('selected_option_id')->references('id')->on('defense_options')->restrictOnDelete();
        });
        Schema::create('defense_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_option_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->boolean('available');
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->unique(['defense_option_id', 'user_id']);
        });
        Schema::create('defense_class_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('course_name')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['teacher_id', 'starts_at', 'ends_at']);
        });
    }
};

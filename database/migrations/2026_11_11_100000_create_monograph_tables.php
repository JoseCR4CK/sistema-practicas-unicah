<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monographs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->unique()->constrained()->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('monograph_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monograph_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('number');
            $table->string('title');
            $table->string('status')->default('En revisión');
            $table->text('observation')->nullable();
            $table->foreignId('submitted_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('submitted_at');
            $table->uuid('submission_key')->unique();
            $table->timestamps();
            $table->unique(['monograph_id', 'number']);
        });
        Schema::create('monograph_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monograph_id')->constrained()->restrictOnDelete();
            $table->foreignId('monograph_version_id')->constrained()->restrictOnDelete();
            $table->string('stage');
            $table->unsignedInteger('attempt')->default(1);
            $table->string('status')->default('Pendiente');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->unique(['monograph_id', 'stage', 'attempt']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->unique()->constrained()->restrictOnDelete();
            $table->string('status')->default('Pendiente de asignación');
            $table->unsignedInteger('revision')->default(0);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_committee_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('function')->default('Miembro');
            $table->timestamps();
            $table->unique(['academic_committee_id', 'user_id']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('practice_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status')->default('Pendiente')->index();
            $table->text('student_observation')->nullable();
            $table->text('observation')->nullable();
            $table->timestamp('submitted_at');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('deadline_restarted_at')->nullable();
            $table->timestamps();
        });
    }
};

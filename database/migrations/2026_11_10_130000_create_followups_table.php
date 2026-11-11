<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->string('type');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('Pendiente')->index();
            $table->text('observation')->nullable();
            $table->unsignedInteger('revision')->default(0);
            $table->uuid('submission_key')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};

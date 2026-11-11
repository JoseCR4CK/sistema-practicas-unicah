<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('Pendiente')->index();
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('observation')->nullable();
            $table->uuid('submission_key')->nullable()->unique();
            $table->timestamps();
        });
    }
};

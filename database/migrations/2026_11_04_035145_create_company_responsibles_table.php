<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_responsibles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('full_name');

            $table->string('position')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->string('status')
                ->default('ACTIVE');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_responsibles');
    }
};
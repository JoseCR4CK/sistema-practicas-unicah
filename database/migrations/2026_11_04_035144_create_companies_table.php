<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('official_name');

            $table->string('commercial_name')->nullable();

            $table->string('rtn')->nullable()->unique();

            $table->string('sector')->nullable();

            $table->string('address')->nullable();

            $table->string('city')->nullable();

            $table->string('department')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->string('status')
                ->default('ACTIVE');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

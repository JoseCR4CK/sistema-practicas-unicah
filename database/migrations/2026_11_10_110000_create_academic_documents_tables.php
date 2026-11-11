<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->restrictOnDelete();
            $table->string('type');
            $table->timestamps();
            $table->unique(['practice_id', 'type']);
        });
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_document_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->string('status')->default('Pendiente de revisión')->index();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('uploaded_at');
            $table->text('submission_note')->nullable();
            $table->text('observation')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['academic_document_id', 'version']);
        });
    }
};

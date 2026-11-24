<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('status', 30)->default('PENDING')->index();
            $table->string('first_names');
            $table->string('last_names');
            $table->string('dni', 40)->unique();
            $table->string('email')->unique();
            $table->string('account_number', 60)->nullable()->unique();
            $table->string('phone', 40);
            $table->foreignId('academic_career_id')->constrained()->restrictOnDelete();
            $table->string('password_hash');
            $table->string('tracking_token_hash', 64);
            $table->boolean('has_pending_subjects')->default(false);
            $table->boolean('induction_verified')->default(false);
            $table->timestamp('induction_verified_at')->nullable();
            $table->foreignId('induction_verified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('correction_notes')->nullable();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->unsignedInteger('revision')->default(0);
            $table->timestamps();
        });
        Schema::create('access_request_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_request_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_subject_id')->constrained()->restrictOnDelete();
            $table->unique(['access_request_id', 'academic_subject_id'], 'access_subject_unique');
        });
        Schema::create('student_pending_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('access_request_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'academic_subject_id'], 'student_pending_subject_unique');
        });
        Schema::create('access_request_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_request_id')->constrained()->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('action');
            $table->string('before_status')->nullable();
            $table->string('after_status');
            $table->text('note')->nullable();
            $table->timestamps();
        });
        Schema::table('academic_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('practice_id')->nullable()->change();
            $table->foreignId('access_request_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->restrictOnDelete();
            $table->unique(['access_request_id', 'type'], 'access_document_unique');
        });
        Schema::table('document_versions', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable()->change();
            $table->json('files')->nullable();
        });
    }

    public function down(): void
    {
        // Evitar una reversión que pierda expedientes ya recibidos o documentos sin práctica.
        if (DB::table('access_requests')->exists() || DB::table('academic_documents')->whereNull('practice_id')->exists()) {
            throw new RuntimeException('Hay solicitudes de acceso: conservar su historial antes de revertir esta migración.');
        }
        Schema::table('document_versions', function (Blueprint $table) {
            $table->dropColumn('files');
            $table->unsignedBigInteger('uploaded_by')->nullable(false)->change();
        });
        Schema::table('academic_documents', function (Blueprint $table) {
            $table->dropUnique('access_document_unique');
            $table->dropConstrainedForeignId('access_request_id');
            $table->dropConstrainedForeignId('student_id');
            $table->unsignedBigInteger('practice_id')->nullable(false)->change();
        });
        Schema::dropIfExists('access_request_events');
        Schema::dropIfExists('student_pending_subjects');
        Schema::dropIfExists('access_request_subject');
        Schema::dropIfExists('access_requests');
    }
};

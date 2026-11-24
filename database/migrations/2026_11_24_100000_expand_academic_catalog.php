<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_careers', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->string('type', 30)->default('FACULTY');
        });
        foreach (DB::table('academic_careers')->get() as $career) {
            DB::table('academic_careers')->where('id', $career->id)->update(['slug' => Str::slug($career->name).'-'.$career->id]);
        }
        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->string('catalog_key', 64)->nullable()->unique();
            $table->string('subject_type', 30)->default('NORMAL');
            $table->unsignedInteger('weekly_minutes')->nullable();
            $table->boolean('review_required')->default(false);
            $table->text('catalog_notes')->nullable();
        });
        Schema::create('academic_career_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_career_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_subject_id')->constrained()->restrictOnDelete();
            $table->string('academic_period')->nullable();
            $table->json('plan_metadata')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['academic_career_id', 'academic_subject_id'], 'career_subject_unique');
        });
        foreach (DB::table('academic_subjects')->get() as $subject) {
            DB::table('academic_career_subject')->insert(['academic_career_id' => $subject->academic_career_id,
                'academic_subject_id' => $subject->id, 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
        Schema::table('students', fn (Blueprint $table) => $table->foreignId('academic_career_id')->nullable()->constrained()->restrictOnDelete());
        Schema::table('teachers', fn (Blueprint $table) => $table->timestamp('access_approved_at')->nullable());
        Schema::create('academic_career_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_career_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['academic_career_id', 'teacher_id'], 'career_teacher_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_career_teacher');
        Schema::table('teachers', fn (Blueprint $table) => $table->dropColumn('access_approved_at'));
        Schema::table('students', fn (Blueprint $table) => $table->dropConstrainedForeignId('academic_career_id'));
        Schema::dropIfExists('academic_career_subject');
        Schema::table('academic_subjects', function (Blueprint $table) {
            $table->dropUnique(['catalog_key']);
            $table->dropColumn(['catalog_key', 'subject_type', 'weekly_minutes', 'review_required', 'catalog_notes']);
        });
        Schema::table('academic_careers', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'type']);
        });
    }
};

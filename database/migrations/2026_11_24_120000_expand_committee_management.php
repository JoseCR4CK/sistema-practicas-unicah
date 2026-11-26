<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('academic_committees', 'origin_period')) {
            Schema::table('academic_committees', function (Blueprint $table) {
                $table->string('origin_period')->nullable();
                $table->unsignedSmallInteger('origin_year')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->foreignId('closed_by')->nullable()->constrained('users')->restrictOnDelete();
                $table->text('closure_reason')->nullable();
                $table->boolean('final_records_verified')->default(false);
            });
        }
        if (! Schema::hasColumn('committee_members', 'assignment_revision')) {
            Schema::table('committee_members', function (Blueprint $table) {
                $table->foreignId('teacher_id')->nullable()->constrained()->restrictOnDelete();
                $table->foreignId('academic_career_id')->nullable()->constrained()->restrictOnDelete();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->unsignedInteger('assignment_revision')->default(0);
            });
        }
        // MySQL necesita un índice sustituto para sostener la FK antes de retirar el anterior.
        if (! Schema::hasIndex('committee_members', 'committee_member_revision_unique')) {
            Schema::table('committee_members', fn (Blueprint $table) => $table->unique(['academic_committee_id', 'user_id', 'assignment_revision'], 'committee_member_revision_unique'));
        }
        if (! Schema::hasIndex('committee_members', 'committee_current_members_index')) {
            Schema::table('committee_members', fn (Blueprint $table) => $table->index(['academic_committee_id', 'ended_at'], 'committee_current_members_index'));
        }
        if (Schema::hasIndex('committee_members', 'committee_members_academic_committee_id_user_id_unique')) {
            Schema::table('committee_members', fn (Blueprint $table) => $table->dropUnique(['academic_committee_id', 'user_id']));
        }
        Schema::create('committee_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_committee_id')->constrained()->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('action');
            $table->text('reason');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
        Schema::table('defenses', function (Blueprint $table) {
            $table->unsignedInteger('scheduling_round')->default(1);
            $table->string('result')->nullable();
            $table->foreignId('result_recorded_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('result_recorded_at')->nullable();
            $table->text('result_note')->nullable();
        });
        Schema::table('defense_options', fn (Blueprint $table) => $table->unsignedInteger('scheduling_round')->default(1));
        foreach (DB::table('academic_committees')->orderBy('id')->get() as $committee) {
            $practice = DB::table('practices')->find($committee->practice_id);
            DB::table('academic_committees')->where('id', $committee->id)->update(['origin_period' => $practice->academic_period, 'origin_year' => $practice->academic_year]);
            $members = DB::table('committee_members')->where('academic_committee_id', $committee->id)->get();
            foreach ($members as $member) {
                DB::table('committee_members')->where('id', $member->id)->update([
                    'teacher_id' => DB::table('teachers')->where('user_id', $member->user_id)->value('id'),
                    'started_at' => $member->created_at,
                ]);
            }
            // No deducir técnico/estilo a partir del orden o de un antiguo "Miembro".
            DB::table('committee_events')->insert(['academic_committee_id' => $committee->id, 'actor_id' => null,
                'action' => 'Historial previo conservado', 'reason' => 'Incorporación técnica del historial existente. Las funciones genéricas y facultades no se reclasificaron por suposición.',
                'before' => json_encode(['committee' => $committee, 'members' => $members], JSON_THROW_ON_ERROR),
                'after' => json_encode(['origin_period' => $practice->academic_period, 'origin_year' => $practice->academic_year], JSON_THROW_ON_ERROR),
                'occurred_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // El esquema anterior no representa sustituciones ni múltiples rondas: impedir pérdida silenciosa.
        if (DB::table('committee_members')->whereNotNull('ended_at')->exists()
            || DB::table('committee_events')->where('action', '!=', 'Historial previo conservado')->exists()
            || DB::table('defenses')->whereNotNull('result')->orWhere('scheduling_round', '>', 1)->exists()) {
            throw new RuntimeException('Hay historial avanzado de ternas. Debe preservarse antes de revertir.');
        }
        Schema::table('defense_options', fn (Blueprint $table) => $table->dropColumn('scheduling_round'));
        Schema::table('defenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('result_recorded_by');
            $table->dropColumn(['scheduling_round', 'result', 'result_recorded_at', 'result_note']);
        });
        Schema::dropIfExists('committee_events');
        Schema::table('committee_members', fn (Blueprint $table) => $table->unique(['academic_committee_id', 'user_id']));
        Schema::table('committee_members', function (Blueprint $table) {
            $table->dropUnique('committee_member_revision_unique');
            $table->dropIndex('committee_current_members_index');
            $table->dropConstrainedForeignId('teacher_id');
            $table->dropConstrainedForeignId('academic_career_id');
            $table->dropColumn(['started_at', 'ended_at', 'assignment_revision']);
        });
        Schema::table('academic_committees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('closed_by');
            $table->dropColumn(['origin_period', 'origin_year', 'closed_at', 'closure_reason', 'final_records_verified']);
        });
    }
};

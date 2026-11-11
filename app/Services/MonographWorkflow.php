<?php

namespace App\Services;

use App\Models\Monograph;
use App\Models\MonographVersion;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class MonographWorkflow
{
    public function submit(Practice $practice, User $user, array $fields): Monograph
    {
        return DB::transaction(function () use ($practice, $user, $fields) {
            Practice::whereKey($practice->id)->lockForUpdate()->firstOrFail();
            $monograph = Monograph::firstOrCreate(['practice_id' => $practice->id]);
            $existing = MonographVersion::where('submission_key', $fields['submission_key'])->first();
            if ($existing) {
                abort_unless($existing->monograph_id === $monograph->id, 409);
                return $monograph;
            }
            $current = $monograph->currentVersion;
            $correction = $monograph->currentReviews()->firstWhere('status', 'Requiere corrección');
            abort_if($current && !$correction, 409, 'La versión actual debe revisarse antes de registrar otra.');
            $version = $monograph->versions()->create([
                'number' => ($current?->number ?? 0) + 1,
                'title' => $fields['title'],
                'observation' => $fields['observation'] ?? null,
                'submitted_by' => $user->id,
                'submitted_at' => now(),
                'submission_key' => $fields['submission_key'],
            ]);
            if ($correction) {
                $this->createReview($monograph, $version, $correction->stage, $correction->attempt + 1, true);
            } else {
                foreach (array_keys(Monograph::REVIEW_DAYS) as $stage) {
                    $this->createReview($monograph, $version, $stage, 1, $stage === 'tecnica');
                }
            }

            return $monograph;
        });
    }

    private function createReview(Monograph $monograph, MonographVersion $version, string $stage, int $attempt, bool $start): void
    {
        $monograph->reviews()->create([
            'monograph_version_id' => $version->id,
            'stage' => $stage,
            'attempt' => $attempt,
            'status' => $start ? 'En revisión' : 'Pendiente',
            'reviewer_id' => $this->reviewerId($monograph, $stage),
            'started_at' => $start ? now() : null,
            'due_at' => $start ? now()->addDays(Monograph::REVIEW_DAYS[$stage]) : null,
        ]);
    }

    private function reviewerId(Monograph $monograph, string $stage): ?int
    {
        return match ($stage) {
            'tecnica' => $monograph->practice->teacher?->user_id,
            'coordinador' => Schema::hasTable('academic_committees') ? $monograph->practice->committee?->coordinator()?->id : null,
            default => null,
        };
    }

    public function review(Monograph $monograph, User $actor, array $fields): void
    {
        DB::transaction(function () use ($monograph, $actor, $fields) {
            Practice::whereKey($monograph->practice_id)->lockForUpdate()->firstOrFail();
            $locked = Monograph::whereKey($monograph->id)->lockForUpdate()->firstOrFail();
            $reviews = $locked->currentReviews();
            $review = $reviews->firstWhere('id', (int) $fields['review_id']);
            abort_unless($review && $review->status === 'En revisión', 409, 'La revisión cambió o ya fue resuelta.');
            $manager = in_array($actor->role?->name, ['COORDINACION', 'ROOT'], true);
            $technicalReviewer = $review->stage === 'tecnica' && $actor->hasRole('DOCENTE_SEGUIMIENTO')
                && $locked->practice->teacher?->user_id === $actor->id && $review->reviewer_id === $actor->id;
            $coordinatorId = $this->reviewerId($locked, 'coordinador');
            $coordinatorReviewer = $review->stage === 'coordinador' && !$actor->hasRole('ESTUDIANTE') && $coordinatorId === $actor->id;
            abort_unless($manager || $technicalReviewer || $coordinatorReviewer, 403);
            if ($review->stage === 'coordinador' && $review->reviewer_id !== $coordinatorId) {
                $review->reviewer_id = $coordinatorId;
            }
            if ($review->stage !== 'estilo' && !$review->reviewer_id) {
                throw ValidationException::withMessages(['review_id' => 'Falta identificar al responsable de esta revisión.']);
            }
            $review->update([
                'status' => $fields['status'],
                'observation' => $fields['observation'] ?? null,
                'recorded_by' => $actor->id,
                'completed_at' => now(),
            ]);
            if ($fields['status'] === 'Requiere corrección') {
                $review->version->update(['status' => 'Requiere corrección']);
            }
            if ($fields['status'] === 'Aprobada') {
                $stages = array_keys(Monograph::REVIEW_DAYS);
                $nextStage = $stages[array_search($review->stage, $stages, true) + 1] ?? null;
                $next = $nextStage ? $reviews->get($nextStage) : null;
                if ($next && $next->status === 'Pendiente' && !$next->started_at) {
                    $next->update([
                        'monograph_version_id' => $locked->currentVersion->id,
                        'status' => 'En revisión',
                        'reviewer_id' => $this->reviewerId($locked, $nextStage),
                        'started_at' => now(),
                        'due_at' => now()->addDays(Monograph::REVIEW_DAYS[$nextStage]),
                    ]);
                }
                if (!$nextStage) {
                    $locked->currentVersion->update(['status' => 'Aprobada']);
                }
            }
        });
    }
}

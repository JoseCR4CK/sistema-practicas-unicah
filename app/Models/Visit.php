<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = ['practice_id', 'status', 'scheduled_date', 'completed_date', 'completed_by', 'observation', 'submission_key'];

    protected function casts(): array
    {
        return ['scheduled_date' => 'date', 'completed_date' => 'date'];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function enabledFrom()
    {
        return $this->practice->start_date?->copy()->addDays(Practice::FIRST_VISIT_DAY - 1);
    }

    public function effectiveStatus(): string
    {
        if (in_array($this->status, ['Programada', 'Realizada'], true)) {
            return $this->status;
        }

        return $this->practice->currentDay() >= Practice::FIRST_VISIT_DAY ? 'Habilitada' : 'Pendiente';
    }
}

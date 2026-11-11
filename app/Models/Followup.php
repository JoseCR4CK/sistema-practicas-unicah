<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Followup extends Model
{
    use SoftDeletes;

    protected $fillable = ['practice_id', 'teacher_id', 'date', 'type', 'title', 'description', 'status', 'observation', 'revision', 'submission_key'];

    protected function casts(): array
    {
        return ['date' => 'date', 'revision' => 'integer'];
    }

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}

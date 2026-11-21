<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'professional_student_id',
        'reviewer_type',
        'reviewer_id',
        'reviewed_type',
        'reviewed_id',
        'score',
        'comment',
        'tags',
        'moderation_status',
        'moderated_at',
        'moderated_by',
        'moderation_reason',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'professional_student_id' => 'integer',
            'reviewer_id' => 'integer',
            'reviewed_id' => 'integer',
            'score' => 'integer',
            'tags' => 'array',
            'moderated_at' => 'timestamp',
            'moderated_by' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function professionalStudent(): BelongsTo
    {
        return $this->belongsTo(ProfessionalStudent::class);
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

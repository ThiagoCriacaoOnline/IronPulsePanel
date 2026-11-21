<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'phone',
        'email_verified_at',
        'invite_activated_at',
        'avatar',
        'path_avatar',
        'birth_date',
        'gender',
        'goal',
        'average_rating',
        'ratings_count',
        'registration_source',
        'registered_by_professional_id',
        'terms_accepted_at',
        'terms_version',
        'is_active',
        'blocked_reason',
        'blocked_at',
        'blocked_by',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
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
            'email_verified_at' => 'timestamp',
            'invite_activated_at' => 'timestamp',
            'birth_date' => 'date',
            'average_rating' => 'decimal:2',
            'registered_by_professional_id' => 'integer',
            'terms_accepted_at' => 'timestamp',
            'is_active' => 'boolean',
            'blocked_at' => 'timestamp',
            'blocked_by' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function registeredByProfessional(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    public function blockedBy(): BelongsTo
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

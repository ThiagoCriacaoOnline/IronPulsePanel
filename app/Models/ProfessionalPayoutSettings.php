<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalPayoutSettings extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'professional_profile_id',
        'payout_mode',
        'pix_key_type',
        'pix_key',
        'pix_holder_name',
        'pix_holder_document',
        'custom_fee_percentage',
        'fee_terms_accepted_at',
        'fee_terms_version',
        'is_payout_blocked',
        'blocked_reason',
        'blocked_at',
        'blocked_by',
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
            'professional_profile_id' => 'integer',
            'custom_fee_percentage' => 'decimal:2',
            'fee_terms_accepted_at' => 'timestamp',
            'is_payout_blocked' => 'boolean',
            'blocked_at' => 'timestamp',
            'blocked_by' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function professionalProfile(): BelongsTo
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

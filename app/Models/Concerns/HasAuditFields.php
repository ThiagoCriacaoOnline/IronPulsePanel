<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuditFields
{
    public static function bootHasAuditFields(): void
    {
        static::creating(function ($model) {
            $model->created_by ??= auth()->id();
            $model->updated_by ??= auth()->id();
        });

        static::updating(function ($model) {
            if (! $model->isDirty('updated_by')) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleted(function ($model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            $userId = $model->deleted_by ?? auth()->id();

            if ($userId) {
                $model->newQuery()->withoutGlobalScopes()
                    ->whereKey($model->getKey())
                    ->toBase()
                    ->update(['deleted_by' => $userId]);

                $model->setAttribute('deleted_by', $userId);
                $model->syncOriginalAttribute('deleted_by');
            }
        });

        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
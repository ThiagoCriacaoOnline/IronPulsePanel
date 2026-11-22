<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use LogicException;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'is_system',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public const SUPER_ADMIN_SLUG = 'super-admin';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function isProtected(): bool
    {
        return $this->is_system || $this->slug === self::SUPER_ADMIN_SLUG;
    }

    protected static function booted(): void
    {
        static::deleting(function (Role $role) {
            if ($role->isProtected()) {
                throw new LogicException('Perfis do sistema não podem ser excluídos.');
            }
        });

        static::updating(function (Role $role) {
            $wasProtected = $role->getOriginal('is_system')
                || $role->getOriginal('slug') === self::SUPER_ADMIN_SLUG;

            if ($wasProtected && $role->isDirty(['slug', 'is_system', 'is_active'])) {
                throw new LogicException('Este perfil é protegido e não pode ser alterado nesses campos.');
            }
        });
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

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->wherePivotNull('deleted_at');
    }

    public function syncPermissions(array $permissionIds, ?int $actorId = null): void
    {
        $actorId ??= auth()->id();
        $permissionIds = array_values(array_unique(array_map('intval', $permissionIds)));

        DB::transaction(function () use ($permissionIds, $actorId) {
            $links = RolePermission::withTrashed()
                ->where('role_id', $this->id)
                ->get()
                ->keyBy('permission_id');

            // remove (soft delete) o que saiu da seleção
            foreach ($links as $permissionId => $link) {
                if (! $link->trashed() && ! in_array($permissionId, $permissionIds, true)) {
                    $link->forceFill(['deleted_by' => $actorId])->save();
                    $link->delete();
                }
            }

            // cria o que entrou, restaurando vínculos antigos em vez de duplicar
            foreach ($permissionIds as $permissionId) {
                $link = $links->get($permissionId);

                if (! $link) {
                    RolePermission::forceCreate([
                        'role_id' => $this->id,
                        'permission_id' => $permissionId,
                        'created_by' => $actorId,
                        'updated_by' => $actorId,
                    ]);
                } elseif ($link->trashed()) {
                    $link->restore();
                    $link->forceFill(['updated_by' => $actorId])->save();
                }
            }
        });
    }
}

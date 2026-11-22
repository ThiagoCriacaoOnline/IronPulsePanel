<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'display_name', 'email', 'password', 'avatar', 'path_avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->wherePivotNull('deleted_at');
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->path_avatar
            ? Storage::disk('public')->url($this->path_avatar)
            : ($this->avatar ?: 'https://ui-avatars.com/api/?background=FF5A1F&color=fff&name='.urlencode($this->name)));
    }

    public function syncRole(int $roleId): void
    {
        $actorId = auth()->id();

        DB::transaction(function () use($roleId, $actorId) {
            // Remove (soft delete) vinculo com outros perfis
            UserRole::where('user_id', $this->id)
                ->where('role_id', '!=', $roleId)
                ->get()
                ->each(function (UserRole $link) use ($actorId) {
                    $link->forceFill(['deleted_by' => $actorId])->save();
                    $link->delete();
                });

            $link = UserRole::withTrashed()
                ->where('user_id', $this->id)
                ->where('role_id', $roleId)
                ->first();

            if (!$link) {
                UserRole::forceCreate([
                    'user_id' => $this->id,
                    'role_id' => $roleId,
                    'created_by' => $actorId,
                    'updated_by' => $actorId,
                ]);
            } elseif ($link->trashed()) {
                $link->restore();
                $link->forceFill(['deleted_by' => null, 'updated_by' => $actorId])->save();
            }
        });
    }
}

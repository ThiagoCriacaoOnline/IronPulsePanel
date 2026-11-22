<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends ModulePolicy
{
    protected function module(): string
    {
        return 'users';
    }

    public function update(User $user, Model $record): bool
    {
        return $this->can($user, 'update') && $this->canManage($user, $record);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->can($user, 'delete')
            && $this->canManage($user, $record)
            && ! $user->is($record);
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return $this->delete($user, $record);
    }

    /**
     * Ninguém gerencia um usuário com mais poder do que o próprio.
     *
     * @param  User  $target
     */
    private function canManage(User $actor, Model $target): bool
    {
        if ($actor->isSuperAdmin()) {
            return true;
        }

        if ($target->isSuperAdmin()) {
            return false;
        }

        $targetRoleIds = $target->roles()->pluck('roles.id');

        return Role::query()
            ->assignableBy($actor)
            ->whereIn('id', $targetRoleIds)
            ->count() === $targetRoleIds->count();
    }
}
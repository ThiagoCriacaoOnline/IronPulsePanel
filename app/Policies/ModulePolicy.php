<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class ModulePolicy
{
    /** Prefixo dos slugs de permissão, por exemplo "users" para users.view. */
    abstract protected function module(): string;

    protected function can(User $user, string $action): bool
    {
        return $user->hasPermission($this->module().'.'.$action);
    }

    public function viewAny(User $user): bool
    {
        return $this->can($user, 'view');
    }

    public function view(User $user, Model $record): bool
    {
        return $this->can($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->can($user, 'create');
    }

    public function update(User $user, Model $record): bool
    {
        return $this->can($user, 'update');
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->can($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->can($user, 'delete');
    }

    public function restore(User $user, Model $record): bool
    {
        return $this->can($user, 'delete');
    }

    public function restoreAny(User $user): bool
    {
        return $this->can($user, 'delete');
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return $this->can($user, 'delete');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->can($user, 'delete');
    }
}
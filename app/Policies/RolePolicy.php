<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RolePolicy extends ModulePolicy
{
    protected function module(): string
    {
        return 'roles';
    }

    /** @param  Role  $record */
    public function update(User $user, Model $record): bool
    {
        return $this->can($user, 'update')
            && ($record->slug !== Role::SUPER_ADMIN_SLUG || $user->isSuperAdmin());
    }

    /** @param  Role  $record */
    public function delete(User $user, Model $record): bool
    {
        return $this->can($user, 'delete') && ! $record->isProtected();
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return $this->delete($user, $record);
    }
}
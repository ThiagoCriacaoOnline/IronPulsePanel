<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PermissionPolicy extends ModulePolicy
{
    protected function module(): string
    {
        return 'permissions';
    }

    // Permissões nascem do código (seeder/migration): criar ou excluir pelo painel
    // geraria slugs que nenhuma policy verifica.
    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Model $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
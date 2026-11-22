<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use Illuminate\Support\Arr;

class CreateRole extends CreateRecord
{
    protected array $permissionIds = [];
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissionIds = RoleForm::flattenPermissionState(Arr::pull($data, 'permissions_by_module'));

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions($this->permissionIds);
    }
}

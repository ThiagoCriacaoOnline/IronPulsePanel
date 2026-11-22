<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use Illuminate\Support\Arr;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected ?array $permissionIds = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions_by_module'] = $this->record->permissions()
            ->get()
            ->groupBy('module')
            ->map(fn ($permissions) => $permissions->pluck('id')->all())
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $state = Arr::pull($data, 'permissions_by_module');
        $this->permissionIds = $state === null ? null : RoleForm::flattenPermissionState($state);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->permissionIds !== null) {
            $this->record->syncPermissions($this->permissionIds);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

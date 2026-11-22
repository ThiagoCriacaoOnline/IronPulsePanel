<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateUser extends CreateRecord
{
    protected ?int $roleId = null;
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->roleId = Arr::pull($data, 'role_id');

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncRole($this->roleId);
    }
}

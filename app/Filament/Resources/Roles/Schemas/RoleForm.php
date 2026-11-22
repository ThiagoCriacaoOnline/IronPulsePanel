<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class RoleForm
{
    private const MODULE_LABELS = [
        'users' => 'Usuários',
        'roles' => 'Perfis de acesso',
        'permissions' => 'Permissões',
        'professionals' => 'Profissionais',
        'students' => 'Alunos',
        'ratings' => 'Avaliações',
        'support' => 'Suporte',
        'reports' => 'Relatórios',
        'settings' => 'Configurações',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label("Nome")
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set): void {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state ?? ''));
                            }
                        }),
                TextInput::make('slug')
                    ->label('Identificador')
                    ->helperText('Gerado automaticamente a partir do nome e não pode ser alterado depois.')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(fn (string $operation): bool => $operation === 'create'),
                Textarea::make('description')
                    ->label("Descrição")
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label("Ativo ?")
                    ->required(),
                Section::make('Permissões')
                    ->columnSpanFull()
                    ->icon(Heroicon::OutlinedKey)
                    ->description(fn (?Role $record): string => $record?->slug === Role::SUPER_ADMIN_SLUG
                        ? 'O Super Administrador tem acesso total e suas permissões não são editáveis.'
                        : 'Marque o que este perfil pode fazer.')
                    ->disabled(fn (?Role $record): bool => $record?->slug === Role::SUPER_ADMIN_SLUG)
                    ->schema(static::permissionFieldsets()),
            ]);
    }

    protected static function permissionFieldsets(): array
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy('module')
            ->map(fn ($permissions, string $module) => Fieldset::make(self::MODULE_LABELS[$module] ?? Str::headline($module))
                ->columnSpanFull()
                ->schema([
                    CheckboxList::make("permissions_by_module.{$module}")
                        ->hiddenLabel()
                        ->options($permissions->mapWithKeys(fn (Permission $permission) => [
                            $permission->id => $permission->name.($permission->is_sensitive ? ' (sensível)' : ''),
                        ])->all())
                        ->descriptions($permissions->mapWithKeys(fn (Permission $permission) => [
                            $permission->id => $permission->description,
                        ])->filter()->all())
                        ->columns(2)
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->columnSpanFull(),
                ]))
            ->values()
            ->all();
    }

    public static function flattenPermissionState(?array $state): array
    {
        return collect($state ?? [])->flatten()->map(fn ($id) => (int) $id)->unique()->values()->all();
    }
}

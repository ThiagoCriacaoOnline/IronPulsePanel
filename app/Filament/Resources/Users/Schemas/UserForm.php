<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Foto')
                            ->icon(Heroicon::OutlinedCamera)
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('path_avatar')
                                    ->hiddenLabel()
                                    ->avatar()
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('avatars')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->alignCenter()
                                    ->helperText('JPG ou PNG, até 2 MB.'),
                            ]),

                        Grid::make(1)
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Dados pessoais')
                                    ->icon(Heroicon::OutlinedUser)
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nome')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('display_name')
                                            ->label('Nome de exibição')
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('E-mail')
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->columnSpanFull(),
                                        Select::make('role_id')
                                            ->label('Função')
                                            ->native(false)
                                            ->required()
                                            ->searchable()
                                            ->options(function () {
                                                $isSuperAdmin = auth()->user()
                                                    ->roles()
                                                    ->where('roles.slug', 'super-admin')
                                                    ->exists();

                                                return Role::query()
                                                    ->when(!$isSuperAdmin, fn($query) => $query->where('slug', "!=", 'super-admin'))
                                                    ->pluck('name', 'id');
                                            })
                                    ]),

                                Section::make('Senha')
                                    ->icon(Heroicon::OutlinedLockClosed)
                                    ->description(fn (string $operation): ?string => $operation === 'edit'
                                        ? 'Deixe em branco para manter a senha atual.'
                                        : null)
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('password')
                                            ->label('Senha')
                                            ->password()
                                            ->revealable()
                                            ->minLength(8)
                                            ->confirmed()
                                            ->required(fn (string $operation): bool => $operation === 'create')
                                            ->dehydrated(fn (?string $state): bool => filled($state)),
                                        TextInput::make('password_confirmation')
                                            ->label('Confirmar senha')
                                            ->password()
                                            ->revealable()
                                            ->requiredWith('password')
                                            ->dehydrated(false),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
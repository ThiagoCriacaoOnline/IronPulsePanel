<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Perfil')
                            ->icon(Heroicon::OutlinedUser)
                            ->columnSpan(1)
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->hiddenLabel()
                                    ->circular()
                                    ->imageSize(112)
                                    ->disk('public')
                                    ->state(fn (User $record) => $record->avatar_url)
                                    ->defaultImageUrl(fn (User $record) => 'https://ui-avatars.com/api/?background=FF5A1F&color=fff&name='.urlencode($record->name)),
                                TextEntry::make('name')
                                    ->label('Nome')
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('display_name')
                                    ->label('Nome de exibição')
                                    ->placeholder('Não informado'),
                                TextEntry::make('email')
                                    ->label('E-mail')
                                    ->icon(Heroicon::OutlinedEnvelope)
                                    ->copyable(),
                                IconEntry::make('email_verified')
                                    ->label('E-mail verificado')
                                    ->state(fn (User $record): bool => filled($record->email_verified_at))
                                    ->boolean(),
                            ]),

                        Section::make('Acesso')
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('roles.name')
                                    ->label('Perfis')
                                    ->badge()
                                    ->color('primary')
                                    ->placeholder('Nenhum perfil atribuído'),
                                TextEntry::make('permissions_list')
                                    ->label('Permissões')
                                    ->icon(Heroicon::OutlinedKey)
                                    ->badge()
                                    ->color('gray')
                                    ->state(fn (User $record): array => $record->roles()
                                        ->with('permissions')
                                        ->get()
                                        ->flatMap(fn ($role) => $role->permissions)
                                        ->pluck('name')
                                        ->unique()
                                        ->sort()
                                        ->values()
                                        ->all())
                                    ->limitList(12)
                                    ->expandableLimitedList()
                                    ->placeholder('Nenhuma permissão'),
                            ]),

                        Section::make('Registro')
                            ->icon(Heroicon::OutlinedClock)
                            ->columnSpanFull()
                            ->columns(3)
                            ->collapsed()
                            ->schema([
                                TextEntry::make('email_verified_at')
                                    ->label('E-mail verificado em')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Não verificado'),
                                TextEntry::make('created_at')
                                    ->label('Criado em')
                                    ->dateTime('d/m/Y H:i'),
                                TextEntry::make('updated_at')
                                    ->label('Atualizado em')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ]),
            ]);
    }
}
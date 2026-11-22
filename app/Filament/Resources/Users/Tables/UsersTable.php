<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Usuários internos do sistema IronPulse')
            ->description('Gestão de usuários internos do sistema IronPulse')
            ->loadingSkeleton()
            ->striped()
            ->columns([
                TextColumn::make('name')
                    ->label("Nome")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label("Exibição")
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Perfil')
                    ->badge()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label("Verificado em")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('path_avatar')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->state(fn (User $record) => $record->avatarUrl)
                    ->defaultImageUrl(fn (User $record) => 'https://ui-avatars.com/api/?background=FF5A1F&color=fff&name='.urlencode($record->name)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

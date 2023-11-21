<?php

namespace App\Filament;

use App\Models\Contracts\WithCreator;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class AgapeTable
{
    public static function creatorColumn()
    {
        return TextColumn::make('creator_id')
            ->label(__('attributes.creator'))
            ->formatStateUsing(fn ($record) => filled($record->creator_id) ? $record->creator->name : __('admin.public'))
            ->sortable();
    }

    public static function timestampColumns(bool $showCreation = false, bool $showModification = false)
    {
        return [
            TextColumn::make('created_at')
                ->label(__('attributes.created_at'))
                ->dateTime(__('misc.datetime_format'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: !$showCreation),
            TextColumn::make('updated_at')
                ->label(__('attributes.updated_at'))
                ->dateTime(__('misc.datetime_format'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: !$showModification),
        ];
    }

    public static function makePublicAction()
    {
        return Action::make('make_public')
            ->label(__('admin.make_public'))
            ->icon('fas-lock-open')
            ->color('warning')
            ->hidden(fn (WithCreator $record) => $record->creator_id === null)
            ->requiresConfirmation()
            ->action(fn (WithCreator $record) => $record->makePublic());
    }
}

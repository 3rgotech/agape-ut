<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCallTypeResource\Pages;
use App\Models\ProjectCallType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectCallTypeResource extends Resource
{
    protected static ?string $model = ProjectCallType::class;

    protected static ?string $navigationIcon = 'fas-tags';
    protected static ?int $navigationSort    = 10;

    public static function form(Form $form): Form
    {
        $dynamicAttributes = collect(config('agape.project_types'))->mapWithKeys(fn ($v, $k) => [$k => $v['label']]);
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->label(__('attributes.reference'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('dynamic_attributes')
                    ->label(__('attributes.dynamic_attributes'))
                    ->options($dynamicAttributes)
                    ->required(),
                Forms\Components\Tabs::make(__('admin.translatable_fields'))
                    ->columnSpanFull()
                    ->columns($form->getColumnsConfig())
                    ->tabs(
                        collect(config('agape.languages'))->map(
                            fn ($lang) =>
                            Forms\Components\Tabs\Tab::make(Str::upper($lang))
                                ->schema([
                                    Forms\Components\TextInput::make('label_long.' . $lang)
                                        ->label(__('attributes.label_long'))
                                        ->required(),
                                    Forms\Components\TextInput::make('label_short.' . $lang)
                                        ->label(__('attributes.label_short'))
                                        ->required(),
                                ])
                        )->all()
                    )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('attributes.reference'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('label_short')
                    ->label(__('attributes.label_short'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('label_long')
                    ->label(__('attributes.label_long'))
                    ->searchable(),
                // TODO : count project calls and link to filtered list
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('attributes.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjectCallTypes::route('/'),
            'create' => Pages\CreateProjectCallType::route('/create'),
            'edit'   => Pages\EditProjectCallType::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.project_call_type_plural');
    }

    public static function getModelLabel(): string
    {
        return __('resources.project_call_type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.project_call_type_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.sections.projectcalls');
    }
}

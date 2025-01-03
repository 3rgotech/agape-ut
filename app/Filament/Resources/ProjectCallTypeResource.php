<?php

namespace App\Filament\Resources;

use App\Filament\AgapeApplicationForm;
use App\Filament\AgapeForm;
use App\Filament\AgapeTable;
use App\Filament\Resources\ProjectCallTypeResource\Pages;
use App\Models\ProjectCallType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectCallTypeResource extends Resource
{
    protected static ?string $model = ProjectCallType::class;

    protected static ?string $navigationIcon = 'fas-tags';
    protected static ?int $navigationSort    = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->label(__('attributes.reference'))
                    ->required()
                    ->maxLength(255)
                    ->unique(table: ProjectCallType::class, ignoreRecord: true),
                AgapeForm::translatableFields($form, fn($lang) => [
                    Forms\Components\TextInput::make('label_long.' . $lang)
                        ->label(__('attributes.label_long'))
                        ->required(),
                    Forms\Components\TextInput::make('label_short.' . $lang)
                        ->label(__('attributes.label_short'))
                        ->required(),
                ]),
                Forms\Components\Toggle::make('lab_budget')
                    ->label(__('attributes.lab_budget'))
                    ->helperText(__('admin.project_call_type.lab_budget_help'))
                    ->inline(true)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('dynamic_attributes')
                    ->label(__('attributes.dynamic_attributes.title'))
                    ->addActionLabel(__('admin.dynamic_attributes.create'))
                    ->columnSpanFull()
                    ->itemLabel(fn(array $state) => $state['label']['fr'] ?? $state['label']['en'] ?? '?')
                    ->reorderable()
                    ->reorderableWithButtons()
                    ->reorderableWithDragAndDrop(false)
                    ->collapsible()
                    ->schema([
                        Forms\Components\Hidden::make('slug')
                            ->required(),
                        Forms\Components\Fieldset::make('label')
                            ->label(__('attributes.dynamic_attributes.label'))
                            ->columnSpan(['sm' => 'full', 'md' => 2])
                            ->columns(min(3, count(config('agape.languages'))))
                            ->schema(
                                collect(config('agape.languages'))
                                    ->map(
                                        fn(string $lang) => Forms\Components\TextInput::make('label.' . $lang)
                                            ->label(Str::upper($lang))
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get) => $set('slug', Str::slug($get('label.en'))))
                                            ->validationAttribute(Str::upper($lang))
                                            ->required()
                                    )
                                    ->all()
                            ),
                        Forms\Components\Fieldset::make('location')
                            ->label(__('attributes.dynamic_attributes.location'))
                            ->columnSpan(['sm' => 'full', 'md' => 2])
                            ->columns(min(3, count(config('agape.languages'))))
                            ->schema([
                                Forms\Components\Select::make('section')
                                    ->label(__('attributes.dynamic_attributes.section'))
                                    ->options(__('pages.apply.sections'))
                                    ->live(onBlur: true)
                                    ->required(),
                                Forms\Components\Select::make('after_field')
                                    ->label(__('attributes.dynamic_attributes.after_field'))
                                    ->helperText(__('admin.dynamic_attributes.after_field_help'))
                                    ->options(fn(Get $get) => AgapeApplicationForm::fieldsPerSection()[$get('section')] ?? [])
                                    ->default('')
                                    ->live(onBlur: true)
                                    ->required(false)
                            ]),
                        Forms\Components\Radio::make('type')
                            ->label(__('attributes.dynamic_attributes.type') . ' : ')
                            ->options(__('attributes.dynamic_attributes.types'))
                            ->inline()
                            ->columnSpanFull()
                            ->live(),
                        //  Options for select
                        Forms\Components\Repeater::make('options')
                            ->label(__('attributes.dynamic_attributes.options'))
                            ->addActionLabel(__('admin.dynamic_attributes.add_option'))
                            ->hidden(fn(Get $get) => $get('type') !== 'select')
                            ->itemLabel(fn(array $state) => $state['label']['fr'] ?? $state['label']['en'] ?? '?')
                            ->columns(['default' => 1, 'sm' => 1, 'lg' => 3])
                            ->columnSpanFull()
                            ->collapsible()
                            ->collapsed(fn($record) => $record?->exists)
                            ->reorderableWithButtons()
                            ->reorderableWithDragAndDrop(false)
                            ->schema([
                                Forms\Components\Hidden::make('value'),
                                Forms\Components\Fieldset::make('label')
                                    ->label(__('attributes.dynamic_attributes.option_label'))
                                    ->columnSpan(['sm' => 'full', 'md' => 2])
                                    ->columns(min(3, count(config('agape.languages'))))
                                    ->schema(
                                        collect(config('agape.languages'))
                                            ->map(
                                                fn(string $lang) => Forms\Components\TextInput::make('label.' . $lang)
                                                    ->label(Str::upper($lang))
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get) => $set('value', Str::slug($get('label.en'))))
                                                    ->validationAttribute(Str::upper($lang))
                                                    ->required()
                                            )
                                            ->all()
                                    ),
                            ]),
                        // Choices for checkbox
                        Forms\Components\Repeater::make('choices')
                            ->label(__('attributes.dynamic_attributes.choices'))
                            ->addActionLabel(__('admin.dynamic_attributes.add_choice'))
                            ->hidden(fn(Get $get) => $get('type') !== 'checkbox')
                            ->itemLabel(fn(array $state) => $state['label']['fr'] ?? $state['label']['en'] ?? '?')
                            ->columns(['default' => 1, 'sm' => 1, 'lg' => 3])
                            ->columnSpanFull()
                            ->collapsible()
                            ->collapsed(fn($record) => $record?->exists)
                            ->reorderableWithButtons()
                            ->reorderableWithDragAndDrop(false)
                            ->schema([
                                Forms\Components\Hidden::make('value'),
                                Forms\Components\Fieldset::make('label')
                                    ->label(__('attributes.dynamic_attributes.choice_label'))
                                    ->columnSpan(['sm' => 'full', 'md' => 2])
                                    ->columns(min(3, count(config('agape.languages'))))
                                    ->schema(
                                        collect(config('agape.languages'))
                                            ->map(
                                                fn(string $lang) => Forms\Components\TextInput::make('label.' . $lang)
                                                    ->label(Str::upper($lang))
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn(Forms\Set $set, Forms\Get $get) => $set('value', Str::slug($get('label.en'))))
                                                    ->validationAttribute(Str::upper($lang))
                                                    ->required()
                                            )
                                            ->all()
                                    ),
                                Forms\Components\Fieldset::make('description')
                                    ->label(__('attributes.dynamic_attributes.choice_description'))
                                    ->columnSpan(['sm' => 'full', 'md' => 2])
                                    ->columns(min(3, count(config('agape.languages'))))
                                    ->schema(
                                        collect(config('agape.languages'))
                                            ->map(
                                                fn(string $lang) => Forms\Components\TextInput::make('description.' . $lang)
                                                    ->label(Str::upper($lang))
                                                    ->validationAttribute(Str::upper($lang))
                                                    ->required()
                                            )
                                            ->all()
                                    ),
                            ]),
                        Forms\Components\Toggle::make('multiple')
                            ->label(__('attributes.dynamic_attributes.option_multiple'))
                            ->inline(true)
                            ->hidden(fn(Forms\Get $get) => $get('type') !== 'select'),
                        // Rules
                        Forms\Components\Fieldset::make('rules')
                            ->label(__('attributes.dynamic_attributes.rules'))
                            ->columnSpanFull()
                            ->columns(['default' => 1, 'sm' => 1, 'lg' => 3])
                            ->schema([
                                Forms\Components\Toggle::make('required')
                                    ->label(__('attributes.dynamic_attributes.required'))
                                    ->inline(true),
                                Forms\Components\TextInput::make('minValue')
                                    ->label(__('attributes.dynamic_attributes.min_value'))
                                    ->inlineLabel(true)
                                    ->hidden(fn(Forms\Get $get) => $get('type') !== 'date'),
                                Forms\Components\TextInput::make('maxValue')
                                    ->label(__('attributes.dynamic_attributes.max_value'))
                                    ->inlineLabel(true)
                                    ->hidden(fn(Forms\Get $get) => $get('type') !== 'date'),
                            ]),
                        // Repeatable
                        Forms\Components\Fieldset::make('repeatable')
                            ->label(__('attributes.dynamic_attributes.repeatable_field'))
                            ->columnSpanFull()
                            ->columns(['default' => 1, 'sm' => 1, 'lg' => 3])
                            ->hidden(fn(Forms\Get $get) => !in_array($get('type'), ['text', 'date', 'richtext', 'textarea']))
                            ->schema([
                                Forms\Components\Toggle::make('repeatable')
                                    ->label(__('attributes.dynamic_attributes.repeatable'))
                                    ->live(onBlur: true)
                                    ->inline(true),
                                Forms\Components\TextInput::make('minItems')
                                    ->label(__('attributes.dynamic_attributes.min_items'))
                                    ->integer()
                                    ->minValue(0)
                                    ->inlineLabel(true)
                                    ->hidden(fn(Forms\Get $get) => !$get('repeatable')),
                                Forms\Components\TextInput::make('maxItems')
                                    ->label(__('attributes.dynamic_attributes.max_items'))
                                    ->integer()
                                    ->minValue(0)
                                    ->inlineLabel(true)
                                    ->hidden(fn(Forms\Get $get) => !$get('repeatable')),
                            ]),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 4,
                    ]),
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
                ...AgapeTable::timestampColumns()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'view'   => Pages\ViewProjectCallType::route('/{record}'),
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
    public static function getBreadcrumb(): string
    {
        return __('resources.project_call_type_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.sections.data');
    }
}

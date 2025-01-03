<?php

namespace App\Filament;

use App\Forms\Components\TitledTabs;
use App\Models\User;
use App\Settings\GeneralSettings;
use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AgapeForm
{
    public static function creatorField()
    {
        return Select::make('creator_id')
            ->label(__('attributes.creator'))
            ->relationship('creator', 'id')
            ->options(User::all()->pluck('name', 'id'))
            ->default(Auth::id())
            ->hidden(fn(string $operation) => $operation === 'create');
    }

    public static function fileField(string $fileName): SpatieMediaLibraryFileUpload
    {
        $generalSettings = app(GeneralSettings::class);
        return SpatieMediaLibraryFileUpload::make($fileName)
            ->label(__('attributes.files.' . $fileName))
            ->helperText(
                __('attributes.accepted_extensions', ['extensions' => $generalSettings->{'extensions' . ucfirst($fileName)}])
                    . ' ; '
                    . __('attributes.file_size_limit', ['size' => intval(ini_get('upload_max_filesize'))])
            )
            ->preserveFilenames()
            ->downloadable()
            ->disk('public')
            ->previewable(false)
            ->collection($fileName)
            ->maxSize(10240);
    }

    public static function translatableFields(Form $form, Closure $fields, $descriptionLabel = 'admin.translatable_fields.description'): TitledTabs
    {
        return TitledTabs::make('translatable_fields')
            ->heading(__('admin.translatable_fields.title'))
            ->description(__($descriptionLabel))
            ->collapsible()
            ->icon('fas-language')
            ->columnSpanFull()
            ->columns($form->getColumnsConfig())
            ->tabs(
                collect(config('agape.languages'))->map(
                    fn($lang) =>
                    Tabs\Tab::make(Str::upper($lang))
                        ->icon('flag-4x3-' . match ($lang) {
                            'en'    => 'gb',
                            default => $lang
                        })
                        ->schema($fields($lang))
                )->all()
            );
    }

    public static function richTextEditor($name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'link', 'bulletList', 'orderedList']);
    }

    public static function notationSection(array $default = []): Section
    {
        return Section::make('evaluation')
            ->heading(__('admin.settings.fields.notation'))
            ->icon('fas-pen-to-square')
            ->columns(1)
            ->schema([
                Repeater::make('notation')
                    ->hiddenLabel()
                    ->addActionLabel(__('admin.settings.actions.addNotation'))
                    ->itemLabel(fn(array $state): ?string => (($state['title'] ?? [])['fr']) ?? null)
                    ->columns(['sm' => 1])
                    ->default($default)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Fieldset::make('title')
                            ->label(__('admin.settings.fields.notationTitle'))
                            ->columnSpan(['sm' => 'full', 'md' => 1])
                            ->columns(min(3, count(config('agape.languages'))))
                            ->schema(
                                collect(config('agape.languages'))
                                    ->map(
                                        fn(string $lang) => TextInput::make('title.' . $lang)
                                            ->label(Str::upper($lang))
                                            ->validationAttribute(Str::upper($lang))
                                            ->required()
                                    )
                                    ->all()
                            ),
                        Fieldset::make('description')
                            ->label(__('admin.settings.fields.notationDescription'))
                            ->columnSpan(['sm' => 'full', 'md' => 1])
                            ->columns(min(3, count(config('agape.languages'))))
                            ->schema(
                                collect(config('agape.languages'))
                                    ->map(
                                        fn(string $lang) => static::richTextEditor('description.' . $lang)
                                            ->label(Str::upper($lang))
                                            ->validationAttribute(Str::upper($lang))
                                            ->required()
                                    )
                                    ->all()
                            )
                    ])
            ]);
    }
}

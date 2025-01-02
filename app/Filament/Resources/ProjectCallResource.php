<?php

namespace App\Filament\Resources;

use App\Enums\ProjectCallStatus;
use App\Filament\AgapeApplicationForm;
use App\Filament\AgapeForm;
use App\Filament\AgapeTable;
use App\Filament\Resources\ProjectCallResource\Pages;
use App\Models\Application;
use App\Models\ProjectCall;
use App\Settings\GeneralSettings;
use App\Utils\MimeType;
use Awcodes\Shout\Components\Shout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectCallResource extends Resource
{
    protected static ?string $model = ProjectCall::class;

    protected static ?string $navigationIcon = 'fas-bullhorn';
    protected static ?int $navigationSort    = 10;

    public static function form(Form $form): Form
    {
        $generalSettings = app(GeneralSettings::class);
        $files = collect(['applicationForm', 'financialForm', 'additionalInformation'])
            ->filter(fn($name) => $generalSettings->{'enable' . ucfirst($name)} ?? false);
        return $form
            ->columns([
                'default' => 1,
                'sm'      => 2,
                'lg'      => 4,
            ])
            ->schema([
                Forms\Components\Select::make('project_call_type_id')
                    ->label(__('attributes.project_call_type'))
                    ->columnSpan([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2
                    ])
                    ->relationship('projectCallType', 'label_long->' . app()->getLocale())
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->label(__('attributes.year'))
                    ->columnSpan([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2
                    ])
                    ->numeric()
                    ->minValue(2020)
                    ->step(1)
                    ->default(now()->format('Y'))
                    ->required(),
                Forms\Components\Section::make('dates')
                    ->heading(__('admin.dates'))
                    ->icon('fas-calendar')
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 4,
                    ])
                    ->collapsible()
                    ->schema([
                        Forms\Components\DatePicker::make('application_start_date')
                            ->label(__('attributes.application_start_date'))
                            ->required(),
                        Forms\Components\DatePicker::make('application_end_date')
                            ->label(__('attributes.application_end_date'))
                            ->after('application_start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('evaluation_start_date')
                            ->label(__('attributes.evaluation_start_date'))
                            ->after('application_end_date')
                            ->required(),
                        Forms\Components\DatePicker::make('evaluation_end_date')
                            ->label(__('attributes.evaluation_end_date'))
                            ->after('evaluation_start_date')
                            ->required(),
                    ]),
                AgapeForm::translatableFields($form, fn($lang) => [
                    Forms\Components\TextInput::make('title.' . $lang)
                        ->label(__('attributes.title'))
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description.' . $lang)
                        ->label(__('attributes.description'))
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\RichEditor::make('privacy_clause.' . $lang)
                        ->label(__('attributes.privacy_clause'))
                        ->columnSpan([
                            'default' => 1,
                            'sm'      => 1,
                            'lg'      => 2
                        ])
                        ->required(),
                    Forms\Components\RichEditor::make('invite_email.' . $lang)
                        ->label(__('attributes.invite_email'))
                        ->columnSpan([
                            'default' => 1,
                            'sm'      => 1,
                            'lg'      => 2
                        ])
                        ->required(),
                    Forms\Components\RichEditor::make('help_experts.' . $lang)
                        ->label(__('attributes.help_experts'))
                        ->columnSpan([
                            'default' => 1,
                            'sm'      => 1,
                            'lg'      => 2
                        ])
                        ->required(),
                    Forms\Components\RichEditor::make('help_candidates.' . $lang)
                        ->label(__('attributes.help_candidates'))
                        ->columnSpan([
                            'default' => 1,
                            'sm'      => 1,
                            'lg'      => 2
                        ])
                        ->required(),
                ]),
                AgapeForm::notationSection(default: $generalSettings->notation)
                    ->collapsible()
                    ->description(__('admin.notation_description')),
                Forms\Components\Section::make('files')
                    ->heading(__('admin.files'))
                    ->description(__('admin.files_description'))
                    ->icon('fas-file-lines')
                    ->collapsible()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 3,
                    ])
                    ->schema(
                        $files
                            ->map(
                                fn($fileName) => AgapeForm::fileField($fileName)
                                    ->acceptedFileTypes(MimeType::getByExtensionList($generalSettings->{'extensions' . ucfirst($fileName)} ?? ''))
                            )
                            ->all()
                    ),
                Forms\Components\Section::make('settings')
                    ->heading(__('admin.settings.title'))
                    ->icon('fas-cogs')
                    ->collapsible()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 4,
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('extra_attributes.number_of_documents')
                            ->label(__('attributes.number_of_documents'))
                            ->helperText(__('admin.default_number_help'))
                            ->default($generalSettings->defaultNumberOfDocuments)
                            ->required()
                            ->integer()
                            ->minValue(0),
                        Forms\Components\TextInput::make('extra_attributes.number_of_laboratories')
                            ->label(__('attributes.number_of_laboratories'))
                            ->helperText(__('admin.default_number_help'))
                            ->default($generalSettings->defaultNumberOfLaboratories)
                            ->required()
                            ->integer()
                            ->minValue(0),
                        Forms\Components\TextInput::make('extra_attributes.number_of_study_fields')
                            ->label(__('attributes.number_of_study_fields'))
                            ->helperText(__('admin.default_number_help'))
                            ->default($generalSettings->defaultNumberOfStudyFields)
                            ->required()
                            ->integer()
                            ->minValue(0),
                        Forms\Components\TextInput::make('extra_attributes.number_of_keywords')
                            ->label(__('attributes.number_of_keywords'))
                            ->helperText(__('admin.default_number_help'))
                            ->default($generalSettings->defaultNumberOfKeywords)
                            ->required()
                            ->integer()
                            ->minValue(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('projectCallType.label_short')
                    ->label(__('attributes.project_call_type'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('attributes.reference'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label(__('attributes.year'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('attributes.title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('attributes.status'))
                    ->badge()
                    // ->color(fn (ProjectCallStatus $state) => $state->color())
                    // ->formatStateUsing(fn (ProjectCallStatus $state): string => __($state->label()))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ViewColumn::make('application_start_date')
                    ->label(__('admin.dates'))
                    ->view('filament.tables.columns.projectcall-planning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label(__('resources.application_plural'))
                    ->url(fn(ProjectCall $record): string => route('filament.admin.resources.applications.index') . '?tableFilters[project_call_id][value]=' . $record->id)
                    ->sortable(),
                AgapeTable::creatorColumn()
                    ->toggleable(isToggledHiddenByDefault: true),
                ...AgapeTable::timestampColumns()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label(__('attributes.year'))
                    ->options(
                        ProjectCall::all()->pluck('year', 'year')->unique()->all()
                    ),
                Tables\Filters\Filter::make('status_filter')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label(__('attributes.status'))
                            ->options(ProjectCallStatus::class)
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['status'] === ProjectCallStatus::PLANNED->value,
                                fn(Builder $query): Builder => $query->whereDate('application_start_date', '>=', now()),
                            )
                            ->when(
                                $data['status'] === ProjectCallStatus::APPLICATION->value,
                                fn(Builder $query): Builder => $query->whereDate('application_start_date', '<=', now())
                                    ->whereDate('application_end_date', '>=', now()),
                            )
                            ->when(
                                $data['status'] === ProjectCallStatus::WAITING_FOR_EVALUATION->value,
                                fn(Builder $query): Builder => $query->whereDate('application_end_date', '<=', now())
                                    ->whereDate('evaluation_start_date', '>=', now()),
                            )
                            ->when(
                                $data['status'] === ProjectCallStatus::EVALUATION->value,
                                fn(Builder $query): Builder => $query->whereDate('evaluation_start_date', '<=', now())
                                    ->whereDate('evaluation_end_date', '>=', now()),
                            )
                            ->when(
                                $data['status'] === ProjectCallStatus::FINISHED->value,
                                fn(Builder $query): Builder => $query->whereDate('evaluation_end_date', '<=', now()),
                            );
                    }),
                Tables\Filters\TrashedFilter::make()
                    ->label(__('admin.archived_records.label'))
                    ->trueLabel(__('admin.archived_records.all'))
                    ->falseLabel(__('admin.archived_records.only'))
                    ->placeholder(__('admin.archived_records.with'))
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview_application_form')
                    ->label(__('admin.preview_application_form'))
                    ->form(function (ProjectCall $record, Form $form) {
                        $form->model(new Application(['project_call_id' => $record->id]));
                        return (new AgapeApplicationForm($record, $form))->buildForm();
                    })
                    ->slideOver()
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('admin.close'))
                    ->color(Color::Cyan),
                Tables\Actions\DeleteAction::make()
                    ->label(__('admin.archive'))
                    ->icon('fas-box-archive')
                    ->modalHeading(fn($record) => __('admin.archive') . ' ' . $table->getRecordTitle($record))
                    ->hidden(function (ProjectCall $record) {
                        if ($record->evaluation_end_date >= now()) {
                            return true;
                        }
                        if (!Auth::user()->hasRole('administrator')) {
                            return true;
                        }
                        return $record->trashed();
                    }),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ActionGroup::make([
                    ReplicateAction::make('replicate')
                        ->label(__('admin.replicate'))
                        ->excludeAttributes([
                            'reference',
                            'applications',
                            'applications_count',
                            'status',
                            'creator_id',
                            'created_at',
                            'updated_at',
                            'deleted_at'
                        ])
                        ->mutateRecordDataUsing(function (array $data): array {
                            $data['year'] = now()->format('Y');
                            $data['application_start_date'] = null;
                            $data['application_end_date'] = null;
                            $data['evaluation_start_date'] = null;
                            $data['evaluation_end_date'] = null;
                            return $data;
                        })
                        ->beforeReplicaSaved(function (ProjectCall $record) {
                            // TODO : handle the data update on the replicated model
                            dump($record);
                        })
                        ->form([
                            Shout::make('duplication-info')
                                ->content(__('admin.duplication_info'))
                                ->columnSpanFull()
                                ->icon('fas-info-circle')
                                ->color(Color::Cyan),
                            Forms\Components\TextInput::make('year')
                                ->label(__('attributes.year'))
                                ->columnSpanFull()
                                ->numeric()
                                ->minValue(2020)
                                ->step(1)
                                ->required(),
                            Forms\Components\Section::make('dates')
                                ->heading(__('admin.dates'))
                                ->icon('fas-calendar')
                                ->columns([
                                    'default' => 1,
                                    'sm'      => 2,
                                ])
                                ->collapsible()
                                ->schema([
                                    Forms\Components\DatePicker::make('application_start_date')
                                        ->label(__('attributes.application_start_date'))
                                        ->required()
                                        ->minDate(today()->addDay()->format('Y-m-d')),
                                    Forms\Components\DatePicker::make('application_end_date')
                                        ->label(__('attributes.application_end_date'))
                                        ->after('application_start_date')
                                        ->required(),
                                    Forms\Components\DatePicker::make('evaluation_start_date')
                                        ->label(__('attributes.evaluation_start_date'))
                                        ->after('application_end_date')
                                        ->required(),
                                    Forms\Components\DatePicker::make('evaluation_end_date')
                                        ->label(__('attributes.evaluation_end_date'))
                                        ->after('evaluation_start_date')
                                        ->required(),
                                ])
                        ])
                        ->icon('fas-copy'),
                    Tables\Actions\Action::make('export_zip')
                        ->label(__('admin.zip_export'))
                        ->color(Color::Cyan)
                        ->icon('fas-file-zipper')
                        ->url(fn(ProjectCall $record) => route('export_zip.project_call', ['projectCall' => $record]))
                        ->hidden(fn(ProjectCall $record) => $record->status !== ProjectCallStatus::ARCHIVED && $record->status !== ProjectCallStatus::FINISHED)
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('export_evaluations')
                        ->label(__('admin.application_excel_export'))
                        ->color(Color::Emerald)
                        ->icon('fas-file-excel')
                        ->url(fn(ProjectCall $record) => route('export_application.project_call', ['projectCall' => $record]))
                        ->openUrlInNewTab()
                        ->hidden(fn(ProjectCall $record) => $record->application_start_date > now()),
                    Tables\Actions\Action::make('export_evaluations')
                        ->label(__('admin.evaluation_pdf_export'))
                        ->color(Color::Indigo)
                        ->icon('fas-file-pdf')
                        ->url(fn(ProjectCall $record) => route('export_evaluation.project_call', ['projectCall' => $record]))
                        ->hidden(fn(ProjectCall $record) => $record->status !== ProjectCallStatus::EVALUATION && $record->status !== ProjectCallStatus::ARCHIVED && $record->status !== ProjectCallStatus::FINISHED)
                        ->openUrlInNewTab()
                        ->hidden(fn(ProjectCall $record) => $record->evaluation_start_date > now()),
                    Tables\Actions\Action::make('export_evaluations_anonymous')
                        ->label(__('admin.evaluation_pdf_export_anonymous'))
                        ->color(Color::Indigo)
                        ->icon('fas-file-pdf')
                        ->url(fn(ProjectCall $record) => route('export_evaluation.project_call', ['projectCall' => $record, 'anonymized' => true]))
                        ->hidden(fn(ProjectCall $record) => $record->status !== ProjectCallStatus::EVALUATION && $record->status !== ProjectCallStatus::ARCHIVED && $record->status !== ProjectCallStatus::FINISHED)
                        ->openUrlInNewTab()
                        ->hidden(fn(ProjectCall $record) => $record->evaluation_start_date > now()),
                ])
                    ->dropdownWidth(MaxWidth::Small),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            'index'  => Pages\ListProjectCalls::route('/'),
            'create' => Pages\CreateProjectCall::route('/create'),
            'view'   => Pages\ViewProjectCall::route('/{record}'),
            'edit'   => Pages\EditProjectCall::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.project_call_plural');
    }

    public static function getModelLabel(): string
    {
        return __('resources.project_call');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.project_call_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.sections.projectcalls');
    }
}

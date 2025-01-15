<?php

namespace App\Filament\Resources;

use App\Enums\ProjectCallStatus;
use App\Filament\AgapeApplicationForm;
use App\Filament\AgapeForm;
use App\Filament\AgapeTable;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use App\Models\EvaluationOffer;
use App\Models\ProjectCall;
use App\Models\User;
use App\Utils\EvaluationExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'fas-file-lines';
    protected static ?int $navigationSort    = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            ...(new AgapeApplicationForm($form->getRecord()->projectCall, $form))
                ->buildForm()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('reference', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('projectCall.reference')
                    ->label(__('resources.project_call'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('attributes.reference'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('creator.last_name')
                    ->label(__('admin.roles.applicant'))
                    ->formatStateUsing(fn(Application $application) => $application->creator->name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('creator', fn($query) => $query->whereRaw('CONCAT(first_name, " ", last_name) LIKE ?', ['%' . $search . '%']));
                    }),
                Tables\Columns\TextColumn::make('acronym')
                    ->label(__('attributes.acronym'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mainLaboratory')
                    ->label(__('attributes.main_laboratory'))
                    ->listWithLineBreaks()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->leftJoin('carriers', 'applications.id', '=', 'carriers.application_id')
                            ->leftJoin('laboratories', 'carriers.laboratory_id', '=', 'laboratories.id')
                            ->where('carriers.main_carrier', 1)
                            ->orderBy('laboratories.name', $direction);
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas(
                                'laboratories',
                                fn($query) => $query
                                    ->where('carriers.main_carrier', 1)
                                    ->where('name', 'like', "%{$search}%")
                            );
                    })
                    ->toggleable(isToggledHiddenByDefault: false)
                /* ->searchable()
                    ->sortable() */,
                AgapeTable::submissionStatusColumn(),
                ...AgapeTable::timestampColumns(showModification: true),
                AgapeTable::submittedAtColumn()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_call_id')
                    ->label(__('resources.project_call'))
                    ->options(
                        ProjectCall::withTrashed()
                            ->orderBy('reference', 'desc')
                            ->get()
                            ->mapWithKeys(fn(ProjectCall $projectCall) => [
                                $projectCall->id => $projectCall->reference . ($projectCall->trashed() ? ' (' . __('admin.archived') . ')' : '')
                            ])
                            ->all()
                    ),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->label(__('admin.roles.applicant'))
                    ->options(
                        User::all()->pluck('name', 'id')->unique()->all()
                    )->searchable(),
                Tables\Filters\Filter::make('status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label(__('attributes.status'))
                            ->options([
                                'draft'       => __('admin.submission_status.draft'),
                                'submitted'   => __('admin.submission_status.submitted'),
                                'devalidated' => __('admin.submission_status.devalidated'),
                            ])->searchable()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['status'] === 'draft',
                                fn(Builder $query): Builder => $query->whereNull('submitted_at')->whereNull('devalidation_message'),
                            )
                            ->when(
                                $data['status'] === 'submitted',
                                fn(Builder $query): Builder => $query->whereNotNull('submitted_at'),
                            )
                            ->when(
                                $data['status'] === 'devalidated',
                                fn(Builder $query): Builder => $query->whereNull('submitted_at')->whereNotNull('devalidation_message'),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->hidden(fn(Application $record) => filled($record->submitted_at)),
                Tables\Actions\Action::make('selection_comity')
                    ->label(__('admin.application.add_selection_comity_opinion'))
                    ->icon('fas-users-viewfinder')
                    ->color(Color::Cyan)
                    ->form(fn(Application $record) => [
                        AgapeForm::richTextEditor('selection_comity_opinion')
                            ->label(__('attributes.selection_comity_opinion'))
                            ->required()
                            ->default($record->selection_comity_opinion),
                    ])
                    ->modalSubmitActionLabel(__('Save'))
                    ->action(function (Application $record, array $data) {
                        $record->selection_comity_opinion = $data['selection_comity_opinion'];
                        $record->save();
                    })
                    ->hidden(fn(Application $record) => $record->projectCall->status !== ProjectCallStatus::ARCHIVED && $record->projectCall->status !== ProjectCallStatus::FINISHED)
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('offers')
                    ->label(fn(Application $record) => __('admin.application.offers', ['count' => $record->evaluationOffers->count()]))
                    ->url(fn(Application $record) => route('filament.admin.resources.applications.offers', ['record' => $record]))
                    ->color(Color::Lime)
                    ->icon('fas-user-tie')
                    ->hidden(fn(Application $record) => !$record->projectCall->evaluation_start_date->isPast() || blank($record->submitted_at)),
                Tables\Actions\Action::make('evaluations')
                    ->label(fn(Application $record) => __('admin.application.evaluations', ['count' => $record->evaluations->count()]))
                    ->url(fn(Application $record) => route('filament.admin.resources.applications.evaluations', ['record' => $record]))
                    ->color(Color::Green)
                    ->icon('fas-file-signature')
                    ->hidden(fn(Application $record) => !$record->projectCall->evaluation_start_date->isPast() || blank($record->submitted_at)),
                ...AgapeTable::submissionActions(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('export_application')
                        ->label(__('admin.pdf_export'))
                        ->color(Color::Cyan)
                        ->icon('fas-file-pdf')
                        ->url(fn(Application $record) => route('export_application.application', ['application' => $record]))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('export_application_zip')
                        ->label(__('admin.zip_export'))
                        ->color(Color::Cyan)
                        ->icon('fas-file-zipper')
                        ->url(fn(Application $record) => route('export_zip.application', ['application' => $record]))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('export_evaluations')
                        ->label(__('admin.evaluation_pdf_export'))
                        ->color(Color::Indigo)
                        ->icon('fas-file-pdf')
                        ->url(fn(Application $record) => route('export_evaluation.application', ['application' => $record]))
                        ->hidden(fn(Application $record) => $record->projectCall->status !== ProjectCallStatus::EVALUATION && $record->projectCall->status !== ProjectCallStatus::ARCHIVED && $record->projectCall->status !== ProjectCallStatus::FINISHED)
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('export_evaluations_anonymous')
                        ->label(__('admin.evaluation_pdf_export_anonymous'))
                        ->color(Color::Indigo)
                        ->icon('fas-file-pdf')
                        ->url(fn(Application $record) => route('export_evaluation.application', ['application' => $record, 'anonymized' => true]))
                        ->hidden(fn(Application $record) => $record->projectCall->status !== ProjectCallStatus::EVALUATION && $record->projectCall->status !== ProjectCallStatus::ARCHIVED && $record->projectCall->status !== ProjectCallStatus::FINISHED)
                        ->openUrlInNewTab(),
                ])
                    ->dropdownWidth(MaxWidth::Small)
                    ->hidden(fn(Application $record) => blank($record->submitted_at)),
            ])
            ->bulkActions([]);
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
            'index'       => Pages\ListApplications::route('/'),
            'view'        => Pages\ViewApplication::route('/{record}'),
            'edit'        => Pages\EditApplication::route('/{record}/edit'),
            'offers'      => Pages\ListEvaluationOffers::route('/{record}/offers'),
            'evaluations' => Pages\ListEvaluations::route('/{record}/evaluations'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.application_plural');
    }

    public static function getModelLabel(): string
    {
        return __('resources.application');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.application_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.sections.projectcalls');
    }
}

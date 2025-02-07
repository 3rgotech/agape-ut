<?php

namespace App\Filament;

use App\Enums\JobTitle;
use App\Enums\OrganizationType;
use App\Models\Laboratory;
use App\Models\ProjectCall;
use App\Models\StudyField;
use App\Settings\GeneralSettings;
use App\Utils\Date;
use App\Utils\MimeType;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AgapeApplicationForm
{
    public function __construct(protected ProjectCall $projectCall, protected Forms\Form $form, protected $forEvaluation = false) {}

    public function buildForm(): array
    {
        return [
            Forms\Components\Hidden::make('project_call_id')->default($this->projectCall->id),
            self::buildSection('general'),
            self::buildSection('scientific'),
            self::buildSection('carriers'),
            self::buildSection('budget'),
            self::buildSection('files'),
        ];
    }

    public static function fieldsPerSection(): array
    {
        $generalSettings = app(GeneralSettings::class);
        $budgetFields = [];
        if ($generalSettings->enableBudgetIncomeOutcome) {
            $budgetFields = [
                'total_expected_income'  => __('attributes.total_expected_income'),
                'total_expected_outcome' => __('attributes.total_expected_outcome'),
            ];
        }

        return [
            'general'    => [
                'acronym'      => __('attributes.acronym'),
                'title'        => __('attributes.title'),
                'studyFields'  => __('resources.study_field_plural'),
                'keywords'     => __('attributes.keywords'),
            ],
            'scientific' => [
                'short_description' => __('attributes.short_description'),
                'summary.fr'        => __('attributes.summary_fr'),
                'summary.en'        => __('attributes.summary_en'),
            ],
            'carriers' => [
                'carrier' => false,
            ],
            'budget'     => [
                'amount_requested'  => __('attributes.amount_requested'),
                'other_fundings'    => __('attributes.other_fundings'),
                ...$budgetFields,
                'laboratory_budget' => __('attributes.laboratory_budget'),
            ],
            'files'      => [
                'applicationForm'       => __('attributes.files.applicationForm'),
                'financialForm'         => __('attributes.files.financialForm'),
                'additionalInformation' => __('attributes.files.additionalInformation'),
                'otherAttachments'      => __('attributes.files.otherAttachments'),
            ]
        ];
    }

    public function buildSection(string $section): Forms\Components\Section
    {
        $generalSettings = app(GeneralSettings::class);
        $dynamicFields = collect($this->projectCall->projectCallType->dynamic_attributes ?? [])->filter(fn($field) => $field['section'] === $section);
        $fieldsWithLabels = static::fieldsPerSection()[$section];

        // special case for fields of file section
        if ($section === 'files') {
            $fieldsWithLabels = array_filter(
                $fieldsWithLabels,
                fn($name) => $generalSettings->{'enable' . ucfirst($name)},
                ARRAY_FILTER_USE_KEY
            );
        }
        $sectionFields = collect([]);

        foreach ($fieldsWithLabels as $fieldName => $label) {
            $sectionFields->push(
                $this->getField($fieldName)
                    ?->label($label),
                ...$dynamicFields
                    ->filter(fn($field) => $field['after_field'] === $fieldName)
                    ->map(fn($field) => $this->getDynamicField($field))
                    ->all()
            );
        }
        $sectionFields->push(
            ...$dynamicFields
                ->filter(fn($field) => !in_array($field['after_field'], array_keys($fieldsWithLabels)))
                ->map(fn($field) => $this->getDynamicField($field))
                ->all()
        );

        $sectionFields = $sectionFields->filter(fn($f) => $f !== null);

        return Forms\Components\Section::make($section)
            ->heading(__('pages.apply.sections.' . $section))
            ->hidden($sectionFields->isEmpty())
            ->collapsible()
            ->columns([
                'default' => 1,
                'sm'      => 2,
                'lg'      => 4,
            ])
            ->schema($sectionFields->all());
    }

    public function getField(string $name): null|Forms\Components\Field|Forms\Components\Fieldset
    {
        switch ($name) {
                /**
             * GENERAL SECTION
             */
            case 'acronym':
                return Forms\Components\TextInput::make('acronym')
                    ->columnSpan(['default' => 1, 'sm' => 2, 'lg' => 1])
                    ->required();
            case 'title':
                return Forms\Components\TextInput::make('title')
                    ->columnSpan(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->required();
            case 'carrier':
                return Forms\Components\Repeater::make('carriers')
                    ->relationship('carriers')
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 4])
                    ->addActionLabel(__('pages.apply.add_carrier'))
                    ->deletable()
                    ->itemLabel(fn(array $state): ?string => $state['last_name'] . ' ' . $state['first_name'] ?? null)
                    ->schema([
                        Forms\Components\Hidden::make('id'),
                        Forms\Components\TextInput::make('last_name')
                            ->label(__('attributes.last_name'))
                            ->required(),
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('attributes.first_name'))
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label(__('attributes.email'))
                            ->required()
                            ->email(),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('attributes.phone'))
                            ->required(),
                        Forms\Components\Toggle::make('main_carrier')
                            ->label(__('attributes.main_carrier'))
                            ->live()
                            ->disabled(function (Get $get, $state) {
                                $allCarriers = collect($get('../../carriers'));
                                $countActive = $allCarriers->filter(fn($carrier) => $carrier['main_carrier'] === true)->count();
                                return !$state && $countActive >= 2;
                            })
                            ->helperText(function (Get $get, $state) {
                                $allCarriers = collect($get('../../carriers'));
                                $countActive = $allCarriers->filter(fn($carrier) => $carrier['main_carrier'] === true)->count();
                                return (!$this->forEvaluation && !$state && $countActive >= 2) ? __('pages.apply.main_carrier_help') : null;
                            })
                            ->required(),
                        Forms\Components\Toggle::make('linkedToLaboratory')
                            ->label(__('attributes.linked_to_laboratory'))
                            ->columnSpan(['default' => 1, 'sm' => 1, 'lg' => 3])
                            ->formatStateUsing(fn($record) => $record?->linkedToLaboratory ?? true)
                            ->live()
                            ->default(false)
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) {
                                    $set('laboratory_id', null);
                                    $set('job_title', null);
                                    $set('job_title_other', null);
                                } else {
                                    $set('organization', null);
                                    $set('organization_type', null);
                                    $set('organization_type_other', null);
                                }
                            })
                            ->required(),
                        $this->getField('laboratory')
                            ->columnSpan(['default' => 1, 'sm' => 2, 'lg' => 2])
                            ->hidden(fn(Get $get) => !$get('linkedToLaboratory'))
                            ->required(fn(Get $get) => $get('linkedToLaboratory')),
                        Forms\Components\Select::make('job_title')
                            ->label(__('attributes.job_title'))
                            ->options(JobTitle::class)
                            ->hidden(fn(Get $get) => !$get('linkedToLaboratory'))
                            ->required(fn(Get $get) => $get('linkedToLaboratory'))
                            ->live(),
                        Forms\Components\TextInput::make('job_title_other')
                            ->label(__('attributes.job_title_other'))
                            ->hidden(fn(Get $get) => !$get('linkedToLaboratory') || $get('job_title') !== JobTitle::OTHER->value)
                            ->required(fn(Get $get) => $get('linkedToLaboratory') && $get('job_title') === JobTitle::OTHER->value),
                        Forms\Components\TextInput::make('organization')
                            ->label(__('attributes.organization'))
                            ->columnSpan(['default' => 1, 'sm' => 2, 'lg' => 2])
                            ->hidden(fn(Get $get) => $get('linkedToLaboratory'))
                            ->required(fn(Get $get) => !$get('linkedToLaboratory')),
                        Forms\Components\Select::make('organization_type')
                            ->label(__('attributes.organization_type'))
                            ->options(OrganizationType::class)
                            ->hidden(fn(Get $get) => $get('linkedToLaboratory'))
                            ->required(fn(Get $get) => !$get('linkedToLaboratory'))
                            ->live(),
                        Forms\Components\TextInput::make('organization_type_other')
                            ->label(__('attributes.organization_type_other'))
                            ->hidden(fn(Get $get) => $get('linkedToLaboratory') || $get('organization_type') !== OrganizationType::OTHER->value)
                            ->required(fn(Get $get) => !$get('linkedToLaboratory') && $get('organization_type') === OrganizationType::OTHER->value),
                    ]);
            case 'laboratory_budget':
                if (!$this->projectCall->projectCallType->lab_budget) {
                    return null;
                }
                return Forms\Components\Fieldset::make('laboratory_budget')
                    ->label(__('attributes.laboratory_budget'))
                    ->schema([
                        Forms\Components\Toggle::make('managing_structure_is_lab')
                            ->label(__('attributes.managing_structure_is_lab'))
                            ->live()
                            ->formatStateUsing(fn($record) => $record?->managingStructureIsLaboratory ?? true)
                            ->default(true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $entries = $get('laboratory_budget');
                                foreach ($entries as $key => $entry) {
                                    if (!$state) {
                                        $set('laboratory_budget.' . $key . '.laboratory_id', null);
                                    } else {
                                        $set('laboratory_budget.' . $key . '.organization', null);
                                    }
                                }
                            }),
                        Forms\Components\Repeater::make('laboratory_budget')
                            ->label(false)
                            ->maxItems(2)
                            ->defaultItems(1)
                            ->columnSpanFull()
                            ->columns(['default' => 1, 'sm' => 3])
                            ->addActionLabel(__('pages.apply.add_laboratory_budget'))
                            ->helperText(__('attributes.laboratory_budget_help'))
                            ->deletable()
                            ->itemLabel(fn(array $state): ?string => $state['laboratory'] ?? null)
                            ->schema([
                                Forms\Components\Select::make('laboratory_id')
                                    ->label(__('resources.laboratory'))
                                    ->columnSpanFull()
                                    ->options(function (Get $get) {
                                        $carriers = collect($get('../../carriers'));
                                        return Laboratory::whereIn('id', $carriers->pluck('laboratory_id')->unique()->filter())->pluck('name', 'id');
                                    })
                                    ->distinct()
                                    ->hidden(fn(Get $get) => !$get('../../managing_structure_is_lab'))
                                    ->required(fn(Get $get) => $get('../../managing_structure_is_lab')),
                                Forms\Components\TextInput::make('organization')
                                    ->label(__('attributes.organization'))
                                    ->columnSpanFull()
                                    ->required()
                                    ->distinct()
                                    ->hidden(fn(Get $get) => $get('../../managing_structure_is_lab'))
                                    ->required(fn(Get $get) => !$get('../../managing_structure_is_lab')),
                                Forms\Components\TextInput::make('total_amount')
                                    ->label(__('attributes.total_amount'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('€'),
                                Forms\Components\TextInput::make('hr_expenses')
                                    ->label(__('attributes.hr_expenses'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('€'),
                                Forms\Components\TextInput::make('operating_expenses')
                                    ->label(__('attributes.operating_expenses'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('€'),
                                Forms\Components\TextInput::make('investment_expenses')
                                    ->label(__('attributes.investment_expenses'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('€'),
                                Forms\Components\TextInput::make('internship_expenses')
                                    ->label(__('attributes.internship_expenses'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('€'),
                            ])
                    ]);
            case 'laboratory':
                return Forms\Components\Select::make('laboratory_id')
                    ->label(__('resources.laboratory'))
                    ->relationship(
                        name: 'laboratory',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where(
                            fn(Builder $query) => $query->mine()
                        )
                    )
                    ->getOptionLabelFromRecordUsing(fn(Laboratory $record) => $record->displayName)
                    ->searchable(['name', 'regency', 'unit_code'])
                    ->preload()
                    ->createOptionModalHeading(__('pages.apply.create_laboratory'))
                    ->editOptionModalHeading(__('pages.apply.edit_laboratory'))
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label(__('attributes.name'))
                            ->required(),
                        Forms\Components\TextInput::make('unit_code')
                            ->label(__('attributes.unit_code'))
                            ->required(),
                        Forms\Components\TextInput::make('director_email')
                            ->label(__('attributes.director_email'))
                            ->required()
                            ->email(),
                        Forms\Components\TextInput::make('regency')
                            ->label(__('attributes.regency'))
                            ->required(),
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label(__('attributes.name'))
                            ->disabled()
                            ->required(),
                        Forms\Components\TextInput::make('unit_code')
                            ->label(__('attributes.unit_code'))
                            ->disabled()
                            ->required(),
                        Forms\Components\TextInput::make('director_email')
                            ->label(__('attributes.director_email'))
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('regency')
                            ->label(__('attributes.regency'))
                            ->disabled()
                            ->required(),
                    ]);
            case 'laboratories':
                return Forms\Components\Fieldset::make('laboratories')
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema(array_filter([
                        !$this->forEvaluation ?
                            ($this->projectCall->extra_attributes->number_of_laboratories > 0 ?
                                Forms\Components\Repeater::make('applicationLaboratories')
                                ->label(__('resources.laboratory_plural'))
                                // ->helperText(__('pages.apply.laboratories_help'))
                                ->relationship('applicationLaboratories')
                                ->orderColumn('order')
                                ->defaultItems(1)
                                ->helperText(
                                    Str::of(__('pages.apply.laboratories_help', [
                                        'count' => $this->projectCall->extra_attributes->number_of_laboratories
                                    ]))->toHtmlString()
                                )
                                ->mutateRelationshipDataBeforeSaveUsing(fn(array $data) => filled($data['laboratory_id'] ?? null) ? $data : null)
                                ->mutateRelationshipDataBeforeCreateUsing(fn(array $data) => filled($data['laboratory_id'] ?? null) ? $data : null)
                                ->schema([
                                    $this->getField('laboratory'),
                                    Forms\Components\TextInput::make('contact_name')
                                        ->label(__('attributes.contact_name')),
                                ])
                                ->columnSpanFull()
                                ->columns(['default' => 1, 'sm' => 2, 'lg' => 2])
                                ->reorderable()
                                ->reorderableWithButtons()
                                ->reorderableWithDragAndDrop(false)
                                ->addActionLabel(__('pages.apply.add_laboratory'))
                                ->required()
                                ->minItems(1)
                                ->maxItems($this->projectCall->extra_attributes->number_of_laboratories)
                                : null)
                            :
                            TableRepeater::make('laboratories')
                            ->label(__('resources.laboratory_plural'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('attributes.name')),
                                Forms\Components\TextInput::make('unit_code')
                                    ->label(__('attributes.unit_code')),
                                Forms\Components\TextInput::make('director_email')
                                    ->label(__('attributes.director_email')),
                                Forms\Components\TextInput::make('regency')
                                    ->label(__('attributes.regency')),
                                Forms\Components\TextInput::make('pivot.contact_name')
                                    ->label(__('attributes.contact_name')),
                            ])
                            ->hideLabels(),
                        AgapeForm::richTextEditor('other_laboratories')
                            ->label(__('attributes.other_laboratories'))
                            ->columnSpanFull(),
                    ]));
            case 'keywords':
                return $this->projectCall->extra_attributes->number_of_keywords > 0
                    ? Forms\Components\Repeater::make('keywords')
                    ->addActionLabel(__('pages.apply.add_keyword'))
                    ->simple(
                        Forms\Components\TextInput::make('value'),
                    )
                    ->default([''])
                    ->grid(3)
                    ->reorderable(false)
                    ->columnSpanFull()
                    ->required()
                    ->minItems(1)
                    ->maxItems($this->projectCall->extra_attributes->number_of_keywords)
                    : null;
                /**
                 * SCIENTIFIC SECTION
                 */
            case 'short_description':
                return AgapeForm::richTextEditor('short_description')
                    ->required()
                    ->columnSpanFull();
            case 'summary.fr':
                return AgapeForm::richTextEditor('summary.fr')
                    ->required()
                    ->columnSpan([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2,
                    ])
                    ->helperText(__('attributes.summary_help'));
            case 'summary.en':
                return AgapeForm::richTextEditor('summary.en')
                    ->required()
                    ->columnSpan([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2,
                    ])
                    ->helperText(__('attributes.summary_help'));
            case 'studyFields':
                return $this->projectCall->extra_attributes->number_of_study_fields > 0
                    ? Forms\Components\Select::make('studyFields')
                    ->helperText(
                        $this->forEvaluation
                            ? false
                            : __('pages.apply.study_fields_help', ['count' => $this->projectCall->extra_attributes->number_of_study_fields])
                    )
                    ->multiple()
                    ->relationship(
                        name: 'studyFields',
                        modifyQueryUsing: fn(Builder $query) => $query->mine()
                    )
                    ->getOptionLabelFromRecordUsing(fn(StudyField $record) => $record->name)
                    ->columnSpanFull()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->minItems(1)
                    ->maxItems($this->projectCall->extra_attributes->number_of_study_fields)
                    ->createOptionForm([
                        AgapeForm::translatableFields($this->form, fn($lang) => [
                            Forms\Components\TextInput::make('name.' . $lang)
                                ->label(__('attributes.name'))
                                ->required(),
                        ], 'pages.apply.create_study_field_help'),
                    ])
                    ->createOptionModalHeading(__('pages.apply.create_study_field'))
                    : null;
                /**
                 * BUDGET SECTION
                 */
            case 'amount_requested':
            case 'other_fundings':
            case 'total_expected_income':
            case 'total_expected_outcome':
                // If we have a helper text in translation, we use it
                $helperText = (!$this->forEvaluation && __('pages.apply.' . $name . '_help') !== 'pages.apply.' . $name . '_help') ? __('pages.apply.' . $name . '_help') : null;

                if ($name === 'amount_requested' && !$this->projectCall->hasMedia('financialForm')) {
                    $helperText = null;
                }

                return Forms\Components\TextInput::make($name)
                    ->label(__('attributes.' . $name))
                    ->helperText($helperText)
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0)
                    ->step(0.01)
                    ->suffix('€')
                    ->columnSpan([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 2,
                    ]);
                /**
                 * FILES SECTION
                 */
            case 'applicationForm':
            case 'financialForm':
            case 'additionalInformation':
            case 'otherAttachments':
                if ($name === 'otherAttachments' && $this->projectCall->extra_attributes->number_of_documents == 0) {
                    return null;
                }
                if ($name !== 'otherAttachments' && !$this->projectCall->hasMedia($name)) {
                    return null;
                }
                $generalSettings = app(GeneralSettings::class);
                $field = AgapeForm::fileField($name)
                    ->required($name !== 'otherAttachments')
                    ->acceptedFileTypes(MimeType::getByExtensionList($generalSettings->{'extensions' . ucfirst($name)} ?? ''))
                    ->columnSpanFull();
                if ($name === 'otherAttachments') {
                    $maxFiles = $this->projectCall->extra_attributes->number_of_documents;
                    $helperText = Str::of($field->getHelperText())
                        ->append('<br/>', __('pages.apply.other_attachments_help', ['count' => $maxFiles]))
                        ->toHtmlString();

                    $field = $field->multiple()
                        ->reorderable()
                        ->maxFiles($maxFiles)
                        ->helperText($helperText);
                } else {
                    $field = $field->hintAction(
                        Action::make('downloadTemplate')
                            ->label(__('pages.apply.download_template'))
                            ->icon('fas-file-download')
                            ->hidden(!$this->projectCall->hasMedia($name))
                            ->action(fn() => $this->projectCall->getFirstMedia($name)->toResponse(request()))
                    );
                }
                if ($this->forEvaluation) {
                    $field->helperText(null)
                        ->hintActions([])
                        ->reorderable(false)
                        ->deletable(false);
                }

                return $field;
            default:
                return null;
        }
    }

    public function getDynamicField(array $settings, string $prefix = 'extra_attributes.'): ?Forms\Components\Field
    {
        if ($settings['repeatable'] ?? false) {
            return $this->getRepeatableField($settings);
        }
        $fieldClass = match ($settings['type']) {
            'text'     => Forms\Components\TextInput::class,
            'date'     => Forms\Components\DatePicker::class,
            'richtext' => Forms\Components\RichEditor::class,
            'textarea' => Forms\Components\Textarea::class,
            'checkbox' => Forms\Components\CheckboxList::class,
            'select'   => Forms\Components\Select::class,
            default    => null,
        };
        if ($fieldClass === null) {
            return null;
        }
        $slug = $settings['slug'] ?? Str::slug($settings['label']['en']);

        $field = $fieldClass::make($prefix . $slug)
            ->label($settings['label'][app()->getLocale()])
            ->required($settings['required'] ?? false)
            ->live()
            /* ->helperText($settings['helper_text'][app()->getLocale()]) */;

        if (in_array($settings['type'], ['text'])) {
            $field = $field->minValue($settings['minValue'] ?? null)
                ->maxValue($settings['maxValue'] ?? null);
        }

        if (in_array($settings['type'], ['date'])) {
            $field = $field->minDate($settings['minValue'] ? Date::parse($settings['minValue']) : null)
                ->maxDate($settings['maxValue'] ? Date::parse($settings['maxValue']) : null)
                ->format(__('misc.date_format'))
                // ->formatStateUsing(fn($state) => filled($state) ? Date::parse($state)?->format(__('misc.date_format')) : null)
            ;
        }

        if ($settings['type'] === 'select') {
            $field = $field
                ->multiple($settings['multiple'] ?? false)
                ->options(
                    collect($settings['options'] ?? [])
                        ->mapWithKeys(fn($o) => [$o['value'] => $o['label'][app()->getLocale()]])->toArray()
                );
        }
        if ($settings['type'] === 'checkbox') {
            $field = $field->options(
                collect($settings['choices'] ?? [])
                    ->mapWithKeys(fn($o) => [$o['value'] => $o['label'][app()->getLocale()]])->toArray()
            )->descriptions(
                collect($settings['choices'] ?? [])
                    ->mapWithKeys(fn($o) => [$o['value'] => $o['description'][app()->getLocale()]])->toArray()
            );
        }

        return $field
            ->columnSpanFull();
    }

    public function getRepeatableField(array $settings): ?Forms\Components\Repeater
    {
        $fieldSettings = Arr::except($settings, ['repeatable']);
        $fieldSettings['slug'] = 'value';
        $field = $this->getDynamicField($fieldSettings, '');
        return Forms\Components\Repeater::make('extra_attributes.' . $settings['slug'])
            ->label($settings['label'][app()->getLocale()])
            ->addActionLabel('+')
            ->reorderable(false)
            ->defaultItems(1)
            ->simple($field)
            ->minItems($settings['minItems'] ?? null)
            ->maxItems($settings['maxItems'] ?? null)
            ->live()
            ->columnSpanFull();
    }

    public static function getExtraAttributes(ProjectCall $projectCall, Forms\Form $form): array
    {
        $formData = $form->getRawState();
        $dynamicFields = collect($projectCall->projectCallType->dynamic_attributes ?? []);
        $extraAttributes = [];
        foreach ($dynamicFields as $field) {
            $value = Arr::get($formData, 'extra_attributes.' . $field['slug']);
            if ($field['repeatable'] ?? false) {
                $value = collect($value)
                    ->map(fn($item) => Arr::get($item, 'value'))
                    ->values()
                    ->all();
            }
            $extraAttributes[$field['slug']] = $value;
        }
        return $extraAttributes;
    }
}

<?php

namespace App\Filament\Applicant\Pages;

use App\Filament\AgapeApplicationForm;
use App\Models\Application;
use App\Models\ProjectCall;
use App\Rulesets\Application as ApplicationRuleset;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Apply extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view            = 'filament.applicant.pages.apply';
    protected static ?int $navigationSort    = 20;

    public ProjectCall $projectCall;
    public ?Application $application = null;

    public ?array $data = [];

    public array $initialState;

    public function mount(): void
    {
        $projectCallId = request()->query('projectCall');
        $this->loadProjectCall(intval($projectCallId));
    }

    public function loadProjectCall(int $projectCallId)
    {
        $projectCall = ProjectCall::with([
            'projectCallType',
            'applications' => function ($query) {
                $query->where('creator_id', Auth::id());
            }
        ])->find($projectCallId);

        $application = $projectCall->getApplication();

        if (blank($application)) {
            $this->application = new Application([
                'project_call_id' => $projectCall->id
            ]);
            self::$title = __('pages.apply.title_create');
        } else {
            $this->application = $application;
            if (filled($this->application->devalidation_message)) {
                self::$title = __('pages.apply.title_correct');
            } else {
                self::$title = __('pages.apply.title_edit');
            }
        }

        $this->projectCall = $projectCall;
        $this->form->fill($this->application->toArray());
        $this->initialState = $this->form->getRawState();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('help')
                ->heading(__('pages.apply.help'))
                ->icon('fas-circle-info')
                ->iconColor(Color::Blue)
                ->collapsible()
                ->schema([
                    Placeholder::make('help')
                        ->hiddenLabel()
                        ->content(new HtmlString($this->projectCall->help_candidates))
                ]),
            ...(new AgapeApplicationForm($this->projectCall, $form))
                ->buildForm(),
            $this->buildActions()
        ])
            ->model($this->application)
            ->statePath('data');
    }

    protected function buildActions(): Actions
    {
        return Actions::make([
            Action::make('back')
                ->label(__('pages.apply.back'))
                ->icon('fas-arrow-left')
                ->color('secondary')
                ->requiresConfirmation(fn (Component $livewire) => $livewire->isDirty())
                ->action(function () {
                    return redirect()->route('filament.applicant.pages.dashboard');
                }),
            Action::make('save')
                ->label(__('pages.apply.save'))
                ->icon('fas-save')
                ->color('primary')
                ->hidden(!$this->projectCall->canApply() || filled($this->application->submitted_at))
                ->action(function (Component $livewire) {
                    $livewire->resetErrorBag();
                    $this->saveDraft();
                }),
            Action::make('submit')
                ->label(__('pages.apply.submit'))
                ->icon('fas-paper-plane')
                ->color('success')
                ->hidden(!$this->projectCall->canApply() || filled($this->application->submitted_at))
                // ->disabled(fn (Component $livewire) => $livewire->isDirty())
                // ->tooltip(__('pages.apply.submit_disabled'))
                ->requiresConfirmation(fn (Component $livewire) => !$livewire->isDirty())
                ->modalIcon(fn (Component $livewire) => !$livewire->isDirty() ? 'fas-paper-plane' : null)
                ->modalIconColor(fn (Component $livewire) => !$livewire->isDirty() ? 'success' : null)
                ->modalHeading(fn (Component $livewire) => !$livewire->isDirty() ? __('pages.apply.submit_confirmation_title') : null)
                ->modalDescription(fn (Component $livewire) => !$livewire->isDirty() ? __('pages.apply.submit_confirmation_text') : null)
                ->modalSubmitActionLabel(fn (Component $livewire) => !$livewire->isDirty() ? __('pages.apply.submit_confirmation_button') : null)
                ->action(function () {
                    $this->submitApplication();
                })
        ])
            ->columnSpanFull()
            ->alignCenter()
            // ->columns([
            //     'default' => 1,
            //     'sm'      => 2,
            //     'lg'      => 4,
            // ])
            ->view('components.filament.actions-container');
    }

    public function saveDraft()
    {
        $fileFields = [
            'applicationForm',
            'financialForm',
            'additionalInformation',
            'otherAttachments',
        ];
        $budgetFields = [
            'total_expected_income',
            'total_expected_outcome',
            'amount_requested',
            'other_fundings'
        ];
        $formData = $this->form->getRawState();

        // Special case for budget fields
        foreach ($budgetFields as $field) {
            if (blank($formData[$field] ?? null)) {
                $formData[$field] = null;
            }
        }

        $this->application->projectCall()->associate($this->projectCall);
        $this->application->fill($formData);
        $this->application->extra_attributes = Arr::get($formData, 'extra_attributes', []);

        $this->application->save();

        $this->application->laboratories()->sync(
            collect($formData['applicationLaboratories'] ?? [])
                ->values()
                ->mapWithKeys(fn ($lab, $i) => [intval($lab['laboratory_id']) => [
                    'contact_name' => $lab['contact_name'],
                    'order' => $i + 1
                ]])->all()
        );
        $this->application->studyFields()->sync(array_map("intval", $formData["studyFields"]));

        // save files
        foreach ($fileFields as $fileFieldName) {
            $files = Arr::get($formData, $fileFieldName, []);
            // Already uploaded file is a string with the Media uuid
            $existingMedia = $this->application->getMedia($fileFieldName);
            foreach ($existingMedia as $media) {
                // Delete media if not in files array
                if (!array_key_exists($media->uuid, $files)) {
                    $media->delete();
                }
            }
            foreach ($files as $file) {
                if ($file instanceof TemporaryUploadedFile) {
                    /** @var TemporaryUploadedFile $file */
                    $this->application->addMedia($file)
                        ->usingName($file->getClientOriginalName())
                        ->toMediaCollection($fileFieldName);
                }
            }
        }

        // Reload form
        $this->loadProjectCall($this->projectCall->id);

        Notification::make()
            ->title(__('pages.apply.save_success'))
            ->success()
            ->send();
    }

    public function submitApplication()
    {
        if ($this->isDirty()) {
            Notification::make()
                ->title(__('pages.apply.save_before_submitting'))
                ->danger()
                ->send();
            throw \Illuminate\Validation\ValidationException::withMessages([]);
        }
        $formData = $this->form->getRawState();

        $validator = Validator::make(
            $formData,
            ApplicationRuleset::rules($this->projectCall),
            ApplicationRuleset::messages($this->projectCall),
            ApplicationRuleset::attributes($this->projectCall),
        );
        if ($validator->fails()) {
            $errors = collect($validator->errors()->messages())
                ->mapWithKeys(fn ($messages, $key) => ['data.' . $key => $messages])->all();
            $this->dispatch('close-modal', id: "{$this->getId()}-form-component-action");

            Notification::make()
                ->title(__('pages.apply.submit_error'))
                ->danger()
                ->send();
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
        $this->application->submit();

        Notification::make()
            ->title(__('pages.apply.submit_success'))
            ->success()
            ->send();

        $this->loadProjectCall($this->projectCall->id);
    }

    public function isDirty(): bool
    {
        return $this->form->getRawState() !== $this->initialState;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}

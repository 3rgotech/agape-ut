<?php

namespace App\Filament\Expert\Pages;

use App\Filament\AgapeForm;
use App\Models\EvaluationOffer;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Dashboard extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.expert.pages.dashboard';
    protected static ?string $navigationIcon = 'fas-home';
    protected static ?int $navigationSort    = 10;

    public array $openOffers = [];
    public array $pendingEvaluation = [];
    public array $doneEvaluation = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->openOffers = EvaluationOffer::affectedToMe()
            ->pendingReply()
            ->get()
            // Keep only offers for call in the evaluation phase
            ->filter(fn(EvaluationOffer $offer) => $offer->application->projectCall->canEvaluate())
            ->all();
        $this->pendingEvaluation = EvaluationOffer::affectedToMe()
            ->accepted()
            ->get()
            // Keep only offers for call in the evaluation phase
            ->filter(fn(EvaluationOffer $offer) => $offer->application->projectCall->canEvaluate())
            ->all();
        $this->doneEvaluation = EvaluationOffer::with('evaluation')
            ->affectedToMe()
            ->evaluationDone()
            ->get()
            // Keep only offers for call NOT in the evaluation phase
            // (but with evaluationDone() scope, it means the evaluation period is in the past and expert has submitted the evaluation)
            ->filter(fn(EvaluationOffer $offer) => !$offer->application->projectCall->canEvaluate())
            ->all();
    }

    public function acceptOfferAction(): Action
    {
        return Action::make('acceptOffer')
            ->label(__('pages.dashboard.expert.accept'))
            ->icon('fas-check')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('pages.dashboard.expert.accept_modal_title'))
            ->modalDescription(function (array $arguments): Htmlable {
                return Str::of(__('pages.dashboard.expert.accept_modal_text'))
                    ->append('<br/>')
                    ->append(
                        Str::of(EvaluationOffer::find($arguments['offer'])->application->projectCall->privacy_clause)
                            ->wrap('<blockquote class="mt-2 text-sm border-l border-gray-400 pl-2">', '</blockquote>')
                    )->toHtmlString();
            })
            ->modalSubmitActionLabel(__('pages.dashboard.expert.accept'))
            ->action(function (array $arguments) {
                $offer = collect($this->openOffers)->firstWhere(fn($o) => $o->id == $arguments['offer']);
                if ($offer !== null) {
                    $offer->accept();
                    $this->loadData();
                }
            });
    }

    public function rejectOfferAction(): Action
    {
        return Action::make('rejectOffer')
            ->label(__('pages.dashboard.expert.reject'))
            ->icon('fas-times')
            ->color('danger')
            ->form([
                AgapeForm::richTextEditor('message')
                    ->label(__('pages.dashboard.expert.reason'))
                    ->required(),
            ])
            ->modalSubmitActionLabel(__('pages.dashboard.expert.reject'))
            ->action(function (array $arguments, array $data) {
                $offer = collect($this->openOffers)->firstWhere(fn($o) => $o->id == $arguments['offer']);
                if ($offer !== null) {
                    $offer->reject($data['message']);
                    $this->loadData();
                }
            });
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.dashboard.title');
    }

    public function getTitle(): string | Htmlable
    {
        return __('admin.dashboard.title');
    }
}

@props(['offer'])

@php
    $id = 'offer-' . md5($offer->id);
    $evaluation = $offer->evaluation;
    $application = $offer->application;
    $projectCall = $application->projectCall;
@endphp

<div class="offer-card-expert w-full rounded-3xl ring-1 ring-gray-200 flex flex-col items-stretch p-2"
    id="{{ $id }}">
    <div
        class="flex-1 px-0 sm:px-8 pt-4 flex flex-col space-y-2 xl:space-y-0 justify-between items-stretch xl:items-center">
        <div class="flex-1 flex flex-row justify-center xl:justify-start items-center xl:items-start space-x-2">
            <h3 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white text-center xl:text-start">
                {{ $projectCall->projectCallType->label_long }} - {{ $projectCall->year }}
            </h3>
            <x-filament.project-call-display-modal :projectCall="$projectCall">
                <button type="button" x-on:click="show = true"
                    class="w-full rounded-full bg-zinc-600 p-2 flex items-center justify-center space-x-2 text-sm font-semibold text-white shadow-sm hover:bg-zinc-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-600">
                    <x-fas-info class="w-4 h-4" />
                    <span class="sr-only">
                        {{ __('pages.dashboard.view_project_call') }}
                    </span>
                </button>
            </x-filament.project-call-display-modal>
        </div>
        @if (filled($projectCall->title))
            <p class="mt-1 text-lg leading-7 text-gray-600 dark:text-gray-400 px-8 text-center xl:text-start">
                {{ $projectCall->title }}
            </p>
        @endif
        <h4
            class="text-base leading-6 text-indigo-600 flex flex-col md:flex-row justify-center items-stretch md:items-center gap-x-4">
            <span class="text-center text-indigo-600 font-semibold">
                {{ __('pages.dashboard.expert.evaluation_period') . ' : ' }}
            </span>
            <span class="text-center flex items-center justify-center gap-x-4">
                <span>
                    {{ $projectCall->evaluation_start_date->format(__('misc.date_format')) }}
                </span>
                <span>
                    <x-fas-arrow-right class="w-3 h-3 text-indigo-600" />
                </span>
                <span class="">
                    {{ $projectCall->evaluation_end_date->format(__('misc.date_format')) }}
                </span>
            </span>
        </h4>
    </div>
    <hr class="mx-16 my-2" />
    <p class="mt-1 text-lg font-bold leading-7 text-gray-800 dark:text-gray-200 px-8">
        {{ __('pages.dashboard.expert.application_by', ['title' => $application->title, 'applicant' => $application->creator->name]) }}
    </p>
    <div class="mt-1 text-base leading-7 text-gray-600 dark:text-gray-400 px-8">
        {!! $application->short_description !!}
    </div>
    <div
        class="py-4 w-full rounded-2xl bg-gray-50 dark:bg-gray-700 text-center ring-1 ring-inset ring-gray-900/5 flex justify-center items-center space-x-4">
        @if ($offer->accepted === null)
            {{ ($this->acceptOfferAction)(['offer' => $offer->id]) }}
            {{ ($this->rejectOfferAction)(['offer' => $offer->id]) }}
        @else
            <div
                class="flex flex-col md:flex-row items-stretch md:items-center justify-center space-y-2 md:space-y-0 md:space-x-4">
                @if ($projectCall->canEvaluate())
                    @if (blank($evaluation))
                        <a href="{{ route('filament.expert.pages.evaluate', ['offer' => $offer]) }}"
                            class="block rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            {{ __('pages.dashboard.expert.create_evaluation') }}
                        </a>
                    @elseif(blank($evaluation->submitted_at))
                        @if (filled($evaluation->devalidation_message))
                            <a href="{{ route('filament.expert.pages.evaluate', ['offer' => $offer]) }}"
                                class="block rounded-md bg-orange-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
                                {{ __('pages.dashboard.expert.correct_evaluation') }}
                            </a>
                        @else
                            <a href="{{ route('filament.expert.pages.evaluate', ['offer' => $offer]) }}"
                                class="block rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                {{ __('pages.dashboard.expert.edit_evaluation') }}
                            </a>
                        @endif
                    @endif
                @endif
                @if (filled($evaluation))
                    <a href="{{ route('filament.expert.pages.evaluate', ['offer' => $offer]) }}"
                        class="block rounded-md bg-green-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                        {{ __('pages.dashboard.expert.view_evaluation') }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

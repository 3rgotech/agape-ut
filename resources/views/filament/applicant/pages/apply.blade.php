<x-filament-panels::page>
    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 shadow sm:rounded-md">
        <div class="grid grid-cols-1 gap-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center">
                {{ $projectCall->projectCallType->label_long }} - {{ $projectCall->year }}
            </h3>
            @if (filled($projectCall->title))
                <p class="text-base leading-7 text-xl font-semibold text-gray-600 dark:text-gray-400 text-center">
                    {{ $projectCall->title }}
                </p>
            @endif
            <div class="flex items-center gap-x-4">
                <div class="h-px flex-auto bg-gray-100 dark:bg-gray-700"></div>
                <h4 class="flex-none text-sm font-semibold leading-6 text-indigo-600">
                    {{ __('pages.dashboard.planning') }}
                </h4>
                <div class="h-px flex-auto bg-gray-100 dark:bg-gray-700"></div>
            </div>
            <div class="flex justify-center">
                <ul role="list"
                    class="grid grid-cols-3 auto-cols-min gap-4 text-sm leading-6 text-gray-600 dark:text-gray-300 sm:gap-x-6 sm:gap-y-4 items-center">
                    <span class="text-center text-indigo-600">
                        {{ __('resources.application') }}
                    </span>
                    <span class="text-center flex items-center">
                        <span>
                            {{ $projectCall->application_start_date->format(__('misc.date_format')) }}
                        </span>
                        <x-fas-arrow-right class="w-3 h-3 text-indigo-600 mx-auto ml-4" />
                    </span>
                    <span class="">
                        {{ $projectCall->application_end_date->format(__('misc.date_format')) }}
                    </span>
                    <span class="text-center text-indigo-600">
                        {{ __('resources.evaluation') }}
                    </span>
                    <span class="text-center flex items-center">
                        <span>
                            {{ $projectCall->evaluation_start_date->format(__('misc.date_format')) }}
                        </span>
                        <x-fas-arrow-right class="w-3 h-3 text-indigo-600 mx-auto ml-4" />
                    </span>
                    <span class="">
                        {{ $projectCall->evaluation_end_date->format(__('misc.date_format')) }}
                    </span>
                </ul>
            </div>
        </div>
    </div>

    <div class="hidden sm:block">
        <div class="pt-2">
            <div class="border-t border-gray-200 dark:border-gray-700"></div>
        </div>
    </div>

    <div class="mt-4 pb-12">
        {{ $this->form }}
    </div>
</x-filament-panels::page>

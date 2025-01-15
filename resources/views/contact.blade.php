<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-slot name="title">
            {!! __('pages.contact.title') !!}
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('success'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('send-contact') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('attributes.name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', auth()->user()?->name)" required
                    autofocus autocomplete="name" />
            </div>

            <div class="mt-6">
                <x-label for="email" value="{{ __('attributes.email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', auth()->user()?->email)"
                    required autocomplete="email" />
            </div>

            <div class="mt-6">
                <x-label for="project_call" value="{{ __('pages.contact.project_call') }}" />
                <x-input id="project_call" class="block mt-1 w-full" type="text" name="project_call"
                    :value="old('project_call')" />
            </div>

            <div class="mt-6">
                <x-label for="project" value="{{ __('pages.contact.project') }}" />
                <x-input id="project" class="block mt-1 w-full" type="text" name="project" :value="old('project')" />
            </div>

            <div class="mt-6">
                <x-label for="message" value="{{ __('pages.contact.message') }}" />
                <textarea rows="4" id="message" name="message" required
                    class="block mt-1 w-full py-1.5 border-0 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm  focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
            </div>

            @guest
                @if (filled(env('HCAPTCHA_SITEKEY', null)) && filled(env('HCAPTCHA_SECRET', null)))
                    <div class="mt-4 flex items-center justify-center">
                        {!! HCaptcha::display() !!}
                    </div>
                @endif
            @endguest

            <div class="mt-6 flex flex-col justify-center items-center space-y-2">
                <x-button type="submit" color="primary" class="w-full">
                    {{ __('pages.contact.send') }}
                </x-button>
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('home') }}">
                    {{ __('pages.generic.back') }}
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

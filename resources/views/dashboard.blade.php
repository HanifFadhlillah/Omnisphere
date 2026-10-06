<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="animation:dashEnter .6s cubic-bezier(.22,1,.36,1) .05s both">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" style="animation:dashEnter .7s cubic-bezier(.22,1,.36,1) .15s both">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="animation:dashEnter .7s cubic-bezier(.22,1,.36,1) .25s both">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes dashEnter {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-app-layout>

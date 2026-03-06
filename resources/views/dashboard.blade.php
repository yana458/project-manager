<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(auth()->user()->hasRole('admin'))
                @include('dashboards.admin')

            @elseif(auth()->user()->hasRole('senior'))
                @includeIf('dashboards.senior.' . $department, 'dashboards.senior.development')

            @elseif(auth()->user()->hasRole('junior'))
                @include('dashboards.junior')

            @else
                @include('dashboards.intern')
            @endif

        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    @php($department = $department ?? auth()->user()->department)
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(auth()->user()->hasRole('admin'))
                @include('dashboards.admin')

            @elseif(auth()->user()->hasRole('senior'))
                @php($dept = $department)

                @if(in_array($dept, ['development','marketing','design']))
                    @include('dashboards.senior.' . $dept)
                @else
                    <div class="bg-white shadow rounded-xl p-6">
                        <h3 class="text-lg font-semibold">Department not set</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Please contact an administrator to assign your department.
                        </p>
                    </div>
                @endif

            @elseif(auth()->user()->hasRole('junior'))
                @include('dashboards.junior')

            @else
                @include('dashboards.intern')
            @endif

        </div>
    </div>
</x-app-layout>
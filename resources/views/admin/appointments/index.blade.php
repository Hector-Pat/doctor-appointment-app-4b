@php
    $filteredPatient = null;

    if (request()->filled('patient_id')) {
        $filteredPatient = \App\Models\Patient::with('user')->find(request('patient_id'));
    }
@endphp

<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Citas Médicas',
        'href' => route('admin.appointments.index'),
    ],
]">

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if (request()->filled('patient_id'))
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3">
                <div class="text-sm text-yellow-800">
                    <span class="font-semibold">Filtro activo:</span>
                    mostrando únicamente las consultas de
                    <span class="font-bold">
                        {{ $filteredPatient?->user?->name ?? 'Paciente no encontrado' }}
                    </span>.
                </div>

                <div>
                    <a
                        href="{{ route('admin.appointments.index') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg border border-yellow-400 text-yellow-900 bg-white hover:bg-yellow-100 transition"
                    >
                        <i class="fa-solid fa-filter-circle-xmark me-2"></i>
                        Quitar filtro
                    </a>
                </div>
            </div>
        @endif

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 dark:text-slate-100 font-bold">
                    Gestión de Citas Médicas
                </h1>
            </div>

            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-lg rounded-sm border border-slate-200 dark:border-slate-700">
            <header class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="font-semibold text-slate-800 dark:text-slate-100">
                    Citas
                    <span class="text-slate-400 dark:text-slate-500 font-medium font-inter"></span>
                </h2>
            </header>

            <div class="p-3">
                @livewire('admin.datatables.appointment-table')
            </div>
        </div>
    </div>
</x-admin-layout>
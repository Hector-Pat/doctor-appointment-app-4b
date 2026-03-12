<x-admin-layout :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Citas Médicas',
        'href' => route('admin.appointments.index'),
    ],
    [
        'name' => 'Editar Cita',
        'href' => '#',
    ],
]">

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        @livewire('admin.appointments.edit-appointment', ['appointment' => $appointment])
    </div>
</x-admin-layout>

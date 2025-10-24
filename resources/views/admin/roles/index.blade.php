<x-admin-layout title="Roles | Healthily" :breadcrumbs="[
    [
        'name'=> 'Dashboard',
        'route'=> route('admin.dashboard'),
    ],
    [
        'name'=> 'Roles',
    ],
]">
   @livewire('admin.datatables.role-table')
</x-admin-layout>

<x-admin-layout :breadcrumbs="[
    [ 'name' => 'Dashboard', 'url' => route('admin.dashboard') ],
    [ 'name' => 'Soporte' ]
]">
    <x-slot name="title">
        Soporte
    </x-slot>


<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Tickets de Soporte</h1>
        </div>
        <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
            <div class="flex items-center mb-4 sm:mb-0">
                <!-- Search and other filters can go here -->
            </div>
            <a href="{{ route('admin.support.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Nuevo Ticket
            </a>
        </div>
    </div>
</div>

<div class="flex flex-col mt-6">
    <div class="overflow-x-auto rounded-lg">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow sm:rounded-lg bg-white dark:bg-gray-800 p-4">
                @livewire('admin.datatables.support-tickets-table')
            </div>
        </div>
    </div>
</div>
</x-admin-layout>

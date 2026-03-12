<div class="flex items-center space-x-2">

    <x-wire-button href="{{ route('admin.doctors.edit', $doctor) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.doctors.schedules', $doctor) }}" teal xs title="Gestor de Horarios">
        <i class="fa-solid fa-calendar-alt"></i>
    </x-wire-button>

</div>

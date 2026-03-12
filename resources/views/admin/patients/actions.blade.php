<div class="flex gap-2">
    <x-wire-button
        sm
        primary
        href="{{ route('admin.patients.edit', $patient) }}">
        Editar
    </x-wire-button>

    <x-wire-button
        sm
        positive
        href="{{ route('admin.patients.book-appointment', $patient) }}"
        title="Agendar Cita">
        <i class="fa-solid fa-calendar-plus"></i> Agendar
    </x-wire-button>

    <x-wire-button
        sm
        warning
        href="{{ route('admin.appointments.index', ['patient_id' => $patient->id]) }}"
        title="Consultas anteriores">
        <i class="fa-solid fa-clock-rotate-left"></i>
    </x-wire-button>

</div>
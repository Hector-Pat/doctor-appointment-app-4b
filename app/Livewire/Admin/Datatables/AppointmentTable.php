<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        $query = Appointment::query()
            ->select('appointments.*')
            ->with(['patient.user', 'doctor.user']);

        if (request()->filled('patient_id')) {
            $query->where('patient_id', request('patient_id'));
        }

        return $query;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),

            Column::make('Paciente')
                ->label(fn ($row) => $row->patient?->user?->name ?? 'N/A'),

            Column::make('Doctor')
                ->label(fn ($row) => $row->doctor?->user?->name ?? 'N/A'),

            Column::make('Fecha', 'appointment_date')
                ->format(fn ($value) => \Carbon\Carbon::parse($value)->format('d/m/Y'))
                ->sortable(),

            Column::make('Hora', 'start_time')
                ->format(fn ($value) => \Carbon\Carbon::parse($value)->format('H:i'))
                ->sortable(),

            Column::make('Hora Fin', 'end_time')
                ->format(fn ($value) => \Carbon\Carbon::parse($value)->format('H:i'))
                ->sortable(),

            Column::make('Estado', 'status')->sortable(),

            Column::make('Acciones')
                ->label(fn ($row) => view('admin.appointments.actions', ['appointment' => $row])),
        ];
    }
}
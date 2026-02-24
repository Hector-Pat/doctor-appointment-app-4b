<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DoctorTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Doctor::query()
            ->select('doctors.*')
            ->with(['user', 'speciality']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),

            Column::make('Nombre')
                ->label(fn ($row) => $row->user?->name ?? 'N/A'),

            Column::make('Email')
                ->label(fn ($row) => $row->user?->email ?? 'N/A'),

            Column::make('Especialidad')
                ->label(fn ($row) => $row->speciality?->name ?? 'N/A'),

            Column::make('Cédula')
                ->label(fn ($row) => $row->medical_license_number ?: 'N/A'),

            Column::make('Acciones')
                ->label(fn ($row) => view('admin.doctors.actions', ['doctor' => $row])),
        ];
    }
}

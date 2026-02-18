<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PatientTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Patient::query()->with('user')
            ->select('patients.*')   // <- clave: incluye user_id SIEMPRE
            ->with('user');

    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")->sortable(),

            Column::make("Nombre")
                ->label(fn($row) => $row->user?->name ?? 'N/A')
                ->sortable(fn(Builder $query, string $direction) =>
                $query->leftJoin('users', 'users.id', '=', 'patients.user_id')
                    ->orderBy('users.name', $direction)
                    ->select('patients.*')
                ),

            Column::make("Email")
                ->label(fn($row) => $row->user?->email ?? 'N/A')
                ->sortable(fn(Builder $query, string $direction) =>
                $query->leftJoin('users', 'users.id', '=', 'patients.user_id')
                    ->orderBy('users.email', $direction)
                    ->select('patients.*')
                ),

            Column::make("Número de id")
                ->label(fn($row) => $row->user?->id_number ?? 'N/A')
                ->sortable(fn(Builder $query, string $direction) =>
                $query->leftJoin('users', 'users.id', '=', 'patients.user_id')
                    ->orderBy('users.id_number', $direction)
                    ->select('patients.*')
                ),

            Column::make("Teléfono")
                ->label(fn($row) => $row->user?->phone ?? 'N/A')
                ->sortable(fn(Builder $query, string $direction) =>
                $query->leftJoin('users', 'users.id', '=', 'patients.user_id')
                    ->orderBy('users.phone', $direction)
                    ->select('patients.*')
                ),

            Column::make("Fecha de creación", "created_at")
                ->sortable()
                ->label(fn($row) => $row->created_at?->format('d/m/Y') ?? 'N/A'),

            Column::make("Acciones")
                ->label(fn($row) => view('admin.patients.actions', ['patient' => $row])),
        ];
    }
}

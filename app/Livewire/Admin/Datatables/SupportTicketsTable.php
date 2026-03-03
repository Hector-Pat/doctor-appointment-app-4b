<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SupportTicketsTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return SupportTicket::query()
            ->select('support_tickets.*')
            ->with(['user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),

            Column::make('Usuario')
                ->label(fn ($row) => $row->user?->name ?? 'N/A'),

            Column::make('Título', 'title')->sortable()->searchable(),

            Column::make('Estado', 'status')->sortable(),

            Column::make('Fecha', 'created_at')
                ->format(fn($value) => $value ? $value->format('d/m/Y H:i') : 'N/A')
                ->sortable(),
        ];
    }
}

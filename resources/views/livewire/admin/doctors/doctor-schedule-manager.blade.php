<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center bg-gray-50 border-b">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Gestor de Horarios
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Configura la disponibilidad semanal para el Dr. {{ $doctor->user->name }}
                    </p>
                </div>
            </div>

            <div class="p-6">
                <!-- Mensajes -->
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

                <!-- Bloque A: Configuración de Rango Horario Base -->
                <div class="mb-8 p-4 border rounded-md bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-700 mb-4">Bloque A: Rango Horario Base</h4>
                    <div class="flex flex-wrap gap-4 items-end">
                        <div class="w-48">
                            <x-input type="time"
                                label="Hora Inicio"
                                placeholder="09:00"
                                wire:model.defer="baseStartTime"
                            />
                        </div>
                        <div class="w-48">
                            <x-input type="time"
                                label="Hora Fin"
                                placeholder="17:00"
                                wire:model.defer="baseEndTime"
                            />
                        </div>
                        <div>
                            <x-button primary wire:click="generateMatrix" spinner="generateMatrix">
                                Generar Matriz
                            </x-button>
                        </div>
                    </div>
                </div>

                <!-- Bloque B: Matriz de Disponibilidad -->
                <div class="mt-6 border-t pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold text-gray-700">Bloque B: Matriz de Disponibilidad</h4>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition">
                                Volver
                            </a>
                            <x-button positive wire:click="saveSchedules" spinner="saveSchedules">
                                Guardar Horarios
                            </x-button>
                        </div>
                    </div>

                    @if(count($timeLabels) > 0)
                        <div class="overflow-x-auto border rounded-lg shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">
                                            Hora / Día
                                        </th>
                                        @foreach($days as $dayId => $dayName)
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r">
                                                {{ $dayName }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($timeLabels as $timeLabel)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900 border-r bg-gray-50 text-center">
                                                {{ $timeLabel }} - {{ \Carbon\Carbon::parse($timeLabel)->addMinutes(15)->format('H:i') }}
                                            </td>
                                            @foreach($days as $dayId => $dayName)
                                                <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-gray-500 border-r">
                                                    <div class="flex justify-center items-center">
                                                        <x-checkbox id="slot_{{$dayId}}_{{$timeLabel}}" wire:model.defer="slots.{{$dayId}}.{{$timeLabel}}" lg />
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded border border-dashed">
                            <p class="text-gray-500">No hay matriz de turnos generada. Configura el bloque A.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

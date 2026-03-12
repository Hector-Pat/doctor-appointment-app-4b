<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Agendar Cita Médica
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Busca disponibilidad para el paciente {{ $patient->user->name }}
                </p>
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
                @if (session()->has('warning'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('warning') }}</span>
                    </div>
                @endif
                <form wire:submit.prevent="searchAvailableDoctors" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input type="date"
                                label="Fecha"
                                wire:model.defer="date"
                                min="{{ now()->format('Y-m-d') }}"
                            />
                        </div>
                        <div>
                            <div class="flex flex-col">
                                <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                                <select id="time" wire:model.defer="time" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Selecciona una hora</option>
                                    @foreach($timeOptions as $timeOption)
                                        <option value="{{ $timeOption->format('H:i') }}">{{ $timeOption->format('H:i') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="flex flex-col">
                                <label for="speciality_id" class="block text-sm font-medium text-gray-700 mb-1">Especialidad (Opcional)</label>
                                <select id="speciality_id" wire:model.defer="speciality_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Cualquier especialidad</option>
                                    @foreach($specialities as $speciality)
                                        <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-4">
                        <x-button primary type="submit" spinner="searchAvailableDoctors" icon="magnifying-glass">
                            Buscar Disponibilidad
                        </x-button>
                    </div>
                </form>

                @if($hasSearched)
                    <div class="mt-8 border-t pt-6">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Doctores Disponibles</h4>
                        
                        @if(count($availableDoctors) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($availableDoctors as $doc)
                                    <div class="bg-white border rounded-lg shadow-sm p-4 flex flex-col items-center text-center">
                                        <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl mb-3">
                                            {{ substr($doc->user->name, 0, 1) }}
                                        </div>
                                        <h5 class="text-lg font-bold text-gray-900">{{ $doc->user->name }}</h5>
                                        <p class="text-sm text-gray-500 mb-2">{{ $doc->speciality->name ?? 'Médico General' }}</p>
                                        
                                        <div class="mt-auto pt-4 w-full">
                                            <x-button positive full wire:click="bookAppointment({{ $doc->id }})" spinner="bookAppointment({{ $doc->id }})" icon="calendar">
                                                Agendar con {{ explode(' ', $doc->user->name)[0] }}
                                            </x-button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 bg-yellow-50 rounded border border-yellow-200">
                                <p class="text-yellow-700">No se encontraron doctores disponibles para el horario y/o especialidad seleccionados.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

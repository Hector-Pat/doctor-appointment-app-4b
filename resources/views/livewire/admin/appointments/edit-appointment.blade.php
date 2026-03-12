<div>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-slate-200">
        <div class="px-4 py-5 sm:px-6 flex justify-between bg-gray-50 border-b">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Detalles de la Cita
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Paciente: {{ $appointment->patient->user->name }} | Dr(a). {{ $appointment->doctor->user->name }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Volver
                </a>
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
            @if (session()->has('warning'))
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <div class="flex flex-col">
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de la Cita</label>
                            <input type="date" id="date" wire:model.defer="date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex flex-col">
                            <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Hora de la Cita</label>
                            <select id="time" wire:model.defer="time" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @foreach($timeOptions as $t)
                                    <option value="{{ $t->format('H:i') }}">{{ $t->format('H:i') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="flex flex-col">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select id="status" wire:model.defer="status" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="scheduled">Programado</option>
                                <option value="completed">Completado</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fa-solid fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

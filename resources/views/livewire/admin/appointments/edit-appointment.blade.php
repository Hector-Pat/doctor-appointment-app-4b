<div class="space-y-6">
    @if (session()->has('success'))
        <div class="p-4 rounded-lg bg-green-100 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 rounded-lg bg-red-100 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Encabezado --}}
    <x-wire-card>
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Cita #{{ $appointment->id }}
                </h2>

                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p><span class="font-semibold">Paciente:</span> {{ $appointment->patient->user->name ?? 'N/A' }}</p>
                    <p><span class="font-semibold">Doctor:</span> {{ $appointment->doctor->user->name ?? 'N/A' }}</p>
                    <p><span class="font-semibold">Fecha actual:</span> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</p>
                    <p><span class="font-semibold">Hora actual:</span> {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="button"
                    wire:click="openMedicalHistory"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-transparent text-black hover:bg-gray-50 transition"
                >
                    <i class="fa-solid fa-folder-open me-2"></i>
                    Ver Historial
                </button>

                <button
                    type="button"
                    wire:click="openPreviousConsultations"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-transparent text-black hover:bg-gray-50 transition"
                >
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    Consultas Anteriores
                </button>
            </div>
        </div>
    </x-wire-card>

    {{-- Datos de la cita --}}
    <x-wire-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-wire-input type="date" label="Fecha" wire:model="date" />

            <x-wire-native-select label="Hora" wire:model="time">
                <option value="">Selecciona una hora</option>
                @foreach ($timeOptions as $option)
                    <option value="{{ $option->format('H:i') }}">
                        {{ $option->format('H:i') }}
                    </option>
                @endforeach
            </x-wire-native-select>

            <x-wire-native-select label="Estatus" wire:model="status">
                <option value="scheduled">Programada</option>
                <option value="completed">Completada</option>
                <option value="cancelled">Cancelada</option>
            </x-wire-native-select>
        </div>
    </x-wire-card>

    {{-- Tabs internas --}}
    <x-wire-card>
        <x-tabs active="consulta">
            <x-slot name="header">
                <x-tabs-link tab="consulta">
                    <i class="fa-solid fa-stethoscope me-2"></i>
                    Consulta
                </x-tabs-link>

                <x-tabs-link tab="receta">
                    <i class="fa-solid fa-pills me-2"></i>
                    Receta
                </x-tabs-link>
            </x-slot>

            <x-tab-content tab="consulta">
                <div class="grid grid-cols-1 gap-4">
                    <x-wire-card>
                        <x-wire-textarea
                            label="Diagnóstico"
                            wire:model="diagnosis"
                            rows="4"
                            placeholder="Captura el diagnóstico..."
                        />
                    </x-wire-card>

                    <x-wire-card>
                        <x-wire-textarea
                            label="Tratamiento"
                            wire:model="treatment"
                            rows="4"
                            placeholder="Captura el tratamiento..."
                        />
                    </x-wire-card>

                    <x-wire-card>
                        <x-wire-textarea
                            label="Notas"
                            wire:model="notes"
                            rows="4"
                            placeholder="Captura notas adicionales..."
                        />
                    </x-wire-card>
                </div>
            </x-tab-content>

            <x-tab-content tab="receta">
                <div class="space-y-6">
                    <x-wire-card>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-wire-input
                                label="Medicamento"
                                wire:model="newMedication"
                                placeholder="Ej. Paracetamol"
                            />

                            <x-wire-input
                                label="Dosis"
                                wire:model="newDosage"
                                placeholder="Ej. 500 mg"
                            />

                            <x-wire-input
                                label="Frecuencia / Duración"
                                wire:model="newFrequencyDuration"
                                placeholder="Ej. Cada 8 horas por 5 días"
                            />
                        </div>

                        <div class="mt-4 flex justify-end">
                            <x-wire-button type="button" wire:click="addPrescription">
                                <i class="fa-solid fa-plus"></i>
                                Añadir medicamento
                            </x-wire-button>
                        </div>
                    </x-wire-card>

                    @forelse ($prescriptionsList as $index => $item)
                        <x-wire-card>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <x-wire-input
                                    label="Medicamento"
                                    wire:model="prescriptionsList.{{ $index }}.medication"
                                />

                                <x-wire-input
                                    label="Dosis"
                                    wire:model="prescriptionsList.{{ $index }}.dosage"
                                />

                                <x-wire-input
                                    label="Frecuencia / Duración"
                                    wire:model="prescriptionsList.{{ $index }}.frequency_duration"
                                />
                            </div>

                            <div class="mt-4 flex justify-end">
                                <x-wire-button type="button" red wire:click="removePrescription({{ $index }})">
                                    <i class="fa-solid fa-trash"></i>
                                    Eliminar
                                </x-wire-button>
                            </div>
                        </x-wire-card>
                    @empty
                        <div class="p-4 rounded-lg bg-gray-50 text-gray-500">
                            No hay medicamentos agregados todavía.
                        </div>
                    @endforelse
                </div>
            </x-tab-content>
        </x-tabs>
    </x-wire-card>

    {{-- Card inferior con acciones finales --}}
    <x-wire-card>
        <div class="flex justify-end gap-3">
            <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">
                Volver
            </x-wire-button>

            <x-wire-button type="button" wire:click="save">
                <i class="fa-solid fa-check"></i>
                Guardar
            </x-wire-button>
        </div>
    </x-wire-card>

    {{-- MODAL: VER HISTORIAL MÉDICO --}}
    @if ($showMedicalHistoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between p-6 border-b">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Historial Médico</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $appointment->patient->user->name ?? 'Paciente' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">

                        <button
                            type="button"
                            wire:click="closeMedicalHistory"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-wire-card>
                        <p class="text-sm font-semibold text-gray-500">Tipo de sangre</p>
                        <p class="mt-2 text-gray-900">{{ $appointment->patient->bloodType->type ?? 'N/A' }}</p>
                    </x-wire-card>

                    <x-wire-card>
                        <p class="text-sm font-semibold text-gray-500">Alergias</p>
                        <p class="mt-2 text-gray-900">{{ $appointment->patient->allergies ?: 'N/A' }}</p>
                    </x-wire-card>

                    <x-wire-card>
                        <p class="text-sm font-semibold text-gray-500">Enfermedades crónicas</p>
                        <p class="mt-2 text-gray-900">{{ $appointment->patient->chronic_conditions ?: 'N/A' }}</p>
                    </x-wire-card>

                    <x-wire-card>
                        <p class="text-sm font-semibold text-gray-500">Antecedentes quirúrgicos</p>
                        <p class="mt-2 text-gray-900">{{ $appointment->patient->surgical_history ?: 'N/A' }}</p>
                    </x-wire-card>

                    <x-wire-card class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-500">Historial familiar</p>
                        <p class="mt-2 text-gray-900">{{ $appointment->patient->family_history ?: 'N/A' }}</p>
                    </x-wire-card>

                    <x-wire-card class="md:col-span-2">
                        <p class="text-sm font-semibold text-gray-500">Observaciones generales</p>
                        <p class="mt-2 text-gray-900">{{ $appointment->patient->observations ?: 'N/A' }}</p>
                    </x-wire-card>
                </div>

                <div class="p-6 border-t flex justify-end">
                    <x-wire-button
                        href="{{ route('admin.patients.edit', $appointment->patient) }}"
                        primary
                    >
                        <i class="fa-solid fa-pen-to-square me-2"></i>
                        Editar historial médico
                    </x-wire-button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: CONSULTAS ANTERIORES --}}
    @if ($showPreviousConsultationsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between p-6 border-b">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Consultas Anteriores</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $appointment->patient->user->name ?? 'Paciente' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">

                        <button
                            type="button"
                            wire:click="closePreviousConsultations"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    @forelse ($previousAppointments as $previous)
                        <x-wire-card>
                            <div class="flex flex-col lg:flex-row lg:justify-between gap-4">
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><span class="font-semibold">Paciente:</span> {{ $previous->patient->user->name ?? 'N/A' }}</p>
                                    <p><span class="font-semibold">Doctor:</span> {{ $previous->doctor->user->name ?? 'N/A' }}</p>
                                    <p><span class="font-semibold">Fecha actual:</span> {{ \Carbon\Carbon::parse($previous->appointment_date)->format('d/m/Y') }}</p>
                                    <p><span class="font-semibold">Hora actual:</span> {{ \Carbon\Carbon::parse($previous->start_time)->format('H:i') }}</p>
                                    <p><span class="font-semibold">Diagnóstico:</span> {{ $previous->diagnosis ?: 'N/A' }}</p>
                                    <p><span class="font-semibold">Tratamiento:</span> {{ $previous->treatment ?: 'N/A' }}</p>
                                    <p><span class="font-semibold">Notas:</span> {{ $previous->notes ?: 'N/A' }}</p>
                                </div>

                                <div class="flex justify-end lg:items-start">
                                    <x-wire-button href="{{ route('admin.appointments.edit', $previous) }}" blue>
                                        <i class="fa-solid fa-up-right-from-square me-2"></i>
                                        Consultar detalle
                                    </x-wire-button>
                                </div>
                            </div>
                        </x-wire-card>
                    @empty
                        <div class="p-4 rounded-lg bg-gray-50 text-gray-500">
                            No hay consultas anteriores registradas para este paciente.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
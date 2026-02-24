<x-admin-layout
    title="Doctores | Healthify"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => 'Editar'],
    ]"
>
    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Header --}}
        <x-wire-card class="mb-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img
                        src="{{ $doctor->user->profile_photo_url }}"
                        alt="{{ $doctor->user->name }}"
                        class="h-20 w-20 rounded-full object-cover object-center mr-4"
                    />

                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $doctor->user->name }}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            Licencia: {{ $doctor->medical_license_number ?: 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">
                        Volver
                    </x-wire-button>

                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Form --}}
        <x-wire-card>
            <div class="grid grid-cols-1 gap-4">
                <x-wire-native-select label="Especialidad" name="speciality_id">
                    <option value="">Selecciona una especialidad</option>
                    @foreach ($specialities as $s)
                    <option value="{{ $s->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $s->id)>
                    {{ $s->name }}
                    </option>
                    @endforeach
                </x-wire-native-select>

                <x-wire-input
                    label="Número de licencia médica"
                    name="medical_license_number"
                    :value="old('medical_license_number', $doctor->medical_license_number)"
                    placeholder="Ej. ABC123456"
                />

                <x-wire-textarea
                    label="Biografía"
                    name="biography"
                    rows="5"
                    placeholder="Escribe una breve biografía"
                >{{ old('biography', $doctor->biography) }}</x-wire-textarea>


            </div>
        </x-wire-card>
    </form>
</x-admin-layout>

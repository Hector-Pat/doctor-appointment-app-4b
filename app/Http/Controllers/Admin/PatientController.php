<?php
// app/Http/Controllers/Admin/PatientController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodType;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        return view('admin.patients.index');
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Patient $patient)
    {
        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        // Evita consultas N+1 en la vista al usar $patient->user
        $patient->load('user');

        $bloodTypes = BloodType::all();

        return view('admin.patients.edit', compact('patient', 'bloodTypes'));
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|max:255',
            'chronic_conditions' => 'nullable|string|max:255',
            'surgical_history' => 'nullable|string|max:255',
            'family_history' => 'nullable|string|max:255',
            'observations' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',

            // Validación flexible: si NO son 10 dígitos, entonces limita a 20 chars.
            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {

                    if ($value === null || $value === '') {
                        return; // vacío permitido
                    }

                    $digits = preg_replace('/\D+/', '', $value);

                    if (strlen($digits) < 10) {
                        $fail('El teléfono debe tener al menos 10 dígitos.');
                    }
                },
            ],


            'emergency_contact_relationship' => 'nullable|string|max:255',
        ]);

        // Guardar con máscara si hay 10 dígitos, si no, guardar tal cual (máx 20 ya validado)
        if (array_key_exists('emergency_contact_phone', $data)) {

            $raw = (string) $data['emergency_contact_phone'];
            $digits = preg_replace('/\D+/', '', $raw);

            if ($digits === '') {

                $data['emergency_contact_phone'] = null;

            } elseif (strlen($digits) === 10) {

                // aplicar máscara automática
                $data['emergency_contact_phone'] = sprintf(
                    '(%s) %s-%s',
                    substr($digits, 0, 3),
                    substr($digits, 3, 3),
                    substr($digits, 6, 4)
                );

            } else {

                // más de 10 dígitos → guardar como texto original
                $data['emergency_contact_phone'] = $raw;

            }
        }


        $patient->update($data);
        session()->flash('swal',[
            'icon' => 'success',
            'title' => 'Paciente actualizado',
            'text' => 'El paciente ha sido actualizado exitosamente',

        ]);
        return redirect()
            ->route('admin.patients.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }


    public function destroy(Patient $patient)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Speciality;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        return view('admin.doctors.index');
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user', 'speciality');
        $specialities = Speciality::orderBy('name')->get();

        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'speciality_id' => 'nullable|exists:specialities,id',
            'medical_license_number' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
        ]);

        $doctor->update($data);
        session()->flash('swal',[
            'icon' => 'success',
            'title' => 'Paciente actualizado',
            'text' => 'El Doctor ha sido actualizado exitosamente',

        ]);
        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'Doctor actualizado correctamente.');


    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.appointments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        return view('admin.appointments.edit', compact('appointment'));
    }

    public function update(Request $request, string $id)
    {
        // Handled by Livewire
    }

    public function destroy(string $id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('success', 'Cita eliminada correctamente.');
    }
}

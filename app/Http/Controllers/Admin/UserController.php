<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8|same:password',
            'id_number' => 'required|string|min:5|max:255|regex:/^[A-za-z0-9]+$/|unique:users',
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'id_number' => $data['id_number'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        // Asignar rol (pivot)
        $user->roles()->attach($data['role_id']);

        // Resolver rol elegido
        $role = Role::find($data['role_id']);

        // Si es Paciente -> crear perfil patient y redirigir a edición
        if ($role && $role->name === 'Paciente') {
            $patient = $user->patient()->create([]);

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Usuario creado correctamente',
                'text' => 'El usuario ha sido creado exitosamente',
            ]);

            return redirect()
                ->route('admin.patients.edit', $patient)
                ->with('success', 'Usuario creado correctamente');
        }

        // Si es Doctor -> crear perfil doctor y redirigir a edición
        if ($role && $role->name === 'Doctor') {
            $doctor = $user->doctor()->firstOrCreate([], [
                'speciality_id' => Speciality::value('id') ?? null, // primera especialidad
                'medical_license_number' => null,
                'biography' => null,
            ]);

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Usuario creado correctamente',
                'text' => 'El usuario ha sido creado exitosamente',
            ]);

            return redirect()
                ->route('admin.doctors.edit', $doctor)
                ->with('success', 'Usuario creado correctamente');
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado correctamente',
            'text' => 'El usuario ha sido creado exitosamente',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validationRules = [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'id_number' => 'required|string|min:5|max:255|regex:/^[A-za-z0-9]+$/|unique:users,id_number,' . $user->id,
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ];

        if ($request->filled('password')) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
            $validationRules['password_confirmation'] = 'required|string|min:8|same:password';
        }

        $data = $request->validate($validationRules);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'id_number' => $data['id_number'],
            'phone' => $data['phone'],
            'address' => $data['address'],
        ]);

        if ($request->filled('password')) {
            $user->password = bcrypt($data['password']);
            $user->save();
        }

        // Actualizar rol
        $user->roles()->sync($data['role_id']);

        // Resolver rol actual (ya actualizado)
        $role = Role::find($data['role_id']);

        // Si ahora es Paciente -> asegurar perfil patient
        if ($role && $role->name === 'Paciente') {
            $user->patient()->firstOrCreate([]);
            // opcional: si dejó de ser doctor, eliminar perfil doctor
            $user->doctor()->delete();
        }

        // Si ahora es Doctor -> asegurar perfil doctor
        if ($role && $role->name === 'Doctor') {
            $user->doctor()->firstOrCreate([], [
                'speciality_id' => Speciality::value('id') ?? null,
                'medical_license_number' => null,
                'biography' => null,
            ]);
            // opcional: si dejó de ser paciente, eliminar perfil patient
            $user->patient()->delete();
        }

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario actualizado correctamente',
            'text' => 'El usuario ha sido actualizado exitosamente',
        ]);

        return redirect()->route('admin.users.edit', $user->id)->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (Auth::id() === $user->id) {
            abort(403, 'No puedes borrar tu propio usuario');
        }

        // borrar perfiles relacionados (evita basura)
        $user->patient()->delete();
        $user->doctor()->delete();

        $user->roles()->detach();
        $user->delete();

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario eliminado correctamente',
            'text' => 'El usuario ha sido eliminado exitosamente',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente');
    }
}

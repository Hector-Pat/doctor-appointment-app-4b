<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

// Gestión de roles
Route::resource('roles', RoleController::class)->names('roles');
Route::resource('users', UserController::class)->names('users');
Route::resource('patients', PatientController::class)->names('patients');
Route::get('patients/{patient}/book-appointment', \App\Livewire\Admin\Patients\BookAppointment::class)->name('patients.book-appointment');
Route::resource('doctors', \App\Http\Controllers\Admin\DoctorController::class)->only(['index','edit','update']);
Route::get('doctors/{doctor}/schedules', \App\Livewire\Admin\Doctors\DoctorScheduleManager::class)->name('doctors.schedules');
Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentController::class);

// Módulo de Soporte
Route::resource('support', \App\Http\Controllers\Admin\SupportTicketController::class)->only(['index', 'create', 'store'])->names('support');

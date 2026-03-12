<?php

namespace App\Livewire\Admin\Patients;

use Livewire\Component;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Speciality;
use Illuminate\Support\Carbon;
use WireUi\Traits\WireUiActions;

class BookAppointment extends Component
{
    use WireUiActions;

    public Patient $patient;

    public $date;
    public $time;
    public $speciality_id;

    public $availableDoctors = [];
    public $hasSearched = false;

    // Generated 15-min interval times
    public $timeOptions = [];

    // Specialities for select
    public $specialities = [];

    public function mount(Patient $patient)
    {
        $this->patient = $patient;
        $this->specialities = Speciality::orderBy('name')->get();
        
        $this->date = now()->format('Y-m-d');
        $this->generateTimeOptions();
    }

    public function generateTimeOptions()
    {
        $this->timeOptions = [];
        $start = Carbon::createFromTimeString('06:00');
        $end = Carbon::createFromTimeString('23:00');

        while ($start <= $end) {
            $this->timeOptions[] = clone $start;
            $start->addMinutes(15);
        }
    }

    public function searchAvailableDoctors()
    {
        $this->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        $searchDate = Carbon::parse($this->date);
        $searchTime = Carbon::createFromFormat('H:i', $this->time);

        if ($searchDate->isToday() && $searchTime->lessThan(now())) {
            session()->flash('error', 'No puedes buscar fechas/horas en el pasado.');
            return;
        }

        // DB day of week 0 = Sunday, 1 = Monday...
        $dayOfWeek = $searchDate->dayOfWeek; // Carbon dayOfWeek 0-6
        
        // 1. Get doctors that schedule covers this time on this day
        $timeString = $searchTime->format('H:i:s');
        
        $query = Doctor::query()
            ->whereHas('schedules', function ($q) use ($dayOfWeek, $timeString) {
                // Must have a schedule for this day where start_time <= searchedTime AND end_time > searchedTime
                $q->where('day_of_week', $dayOfWeek)
                  ->where('start_time', '<=', $timeString)
                  ->where('end_time', '>', $timeString);
            });

        if ($this->speciality_id) {
            $query->where('speciality_id', $this->speciality_id);
        }

        // 2. Exclude doctors that already have an appointment at this exact date and time
        $query->whereDoesntHave('appointments', function ($q) use ($searchDate, $timeString) {
            $q->whereDate('appointment_date', $searchDate->format('Y-m-d'))
              ->where('start_time', $timeString)
              ->whereIn('status', ['scheduled', 'completed']);
        });

        $this->availableDoctors = $query->with('user', 'speciality')->get();
        $this->hasSearched = true;

        if ($this->availableDoctors->isEmpty()) {
            session()->flash('warning', 'No hay doctores disponibles para el horario seleccionado.');
        }
    }

    public function bookAppointment($doctorId)
    {
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return;
        }

        // Double check availability
        $searchDate = Carbon::parse($this->date);
        $timeString = Carbon::createFromFormat('H:i', $this->time)->format('H:i:s');

        $conflict = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $searchDate->format('Y-m-d'))
            ->where('start_time', $timeString)
            ->whereIn('status', ['scheduled', 'completed'])
            ->exists();

        if ($conflict) {
            session()->flash('error', 'Este horario ya acaba de ser ocupado.');
            $this->searchAvailableDoctors();
            return;
        }

        Appointment::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $searchDate->format('Y-m-d'),
            'start_time' => $timeString,
            'end_time' => Carbon::createFromFormat('H:i:s', $timeString)->addMinutes(15)->format('H:i:s'),
            'status' => 'scheduled',
        ]);

        session()->flash('success', "Cita reservada con Dr(a). {$doctor->user->name}");
        
        // Reset and clear results
        $this->hasSearched = false;
        $this->availableDoctors = [];
    }

    public function render()
    {
        return view('livewire.admin.patients.book-appointment')
            ->layout('layouts.admin', [
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
                    ['name' => 'Pacientes', 'href' => route('admin.patients.index')],
                    ['name' => 'Agendar Cita', 'href' => '#'],
                ]
            ]);
    }
}

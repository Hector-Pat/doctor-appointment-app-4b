<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Illuminate\Support\Carbon;
use WireUi\Traits\WireUiActions;

class EditAppointment extends Component
{
    use WireUiActions;

    public Appointment $appointment;

    public $date;
    public $time;
    public $status;

    public $timeOptions = [];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->date = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
        $this->time = Carbon::parse($appointment->start_time)->format('H:i');
        $this->status = $appointment->status;

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

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:scheduled,completed,cancelled'
        ]);

        $searchDate = Carbon::parse($this->date);
        $searchTime = Carbon::createFromFormat('H:i', $this->time);

        // If date/time changed, validate availability
        $timeString = $searchTime->format('H:i:s');
        $currentDateString = Carbon::parse($this->appointment->appointment_date)->format('Y-m-d');
        $currentTimeString = Carbon::parse($this->appointment->start_time)->format('H:i:s');

        if ($searchDate->format('Y-m-d') !== $currentDateString || $timeString !== $currentTimeString) {
            
            if ($searchDate->isToday() && $searchTime->lessThan(now())) {
                session()->flash('error', 'No puedes reprogramar para fechas/horas en el pasado.');
                return;
            }

            // Check doctor schedule
            $dayOfWeek = $searchDate->dayOfWeek;
            $hasSchedule = DoctorSchedule::where('doctor_id', $this->appointment->doctor_id)
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $timeString)
                ->where('end_time', '>', $timeString)
                ->exists();

            if (!$hasSchedule) {
                session()->flash('error', 'El doctor no atiende en ese día/horario.');
                return;
            }

            // Check conflicts
            $conflict = Appointment::where('doctor_id', $this->appointment->doctor_id)
                ->whereDate('appointment_date', $searchDate->format('Y-m-d'))
                ->where('start_time', $timeString)
                ->whereIn('status', ['scheduled', 'completed'])
                ->where('id', '!=', $this->appointment->id)
                ->exists();

            if ($conflict) {
                session()->flash('error', 'Ese horario ya está ocupado por otra cita.');
                return;
            }
        }

        $this->appointment->update([
            'appointment_date' => $searchDate->format('Y-m-d'),
            'start_time' => $timeString,
            'end_time' => $searchTime->copy()->addMinutes(15)->format('H:i:s'),
            'status' => $this->status,
        ]);

        session()->flash('success', 'Cita médica actualizada correctamente.');
        
        return redirect()->route('admin.appointments.index');
    }

    public function render()
    {
        return view('livewire.admin.appointments.edit-appointment');
    }
}

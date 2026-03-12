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

    public $diagnosis;
    public $treatment;
    public $notes;

    public $timeOptions = [];

    public $prescriptionsList = [];

    public $newMedication = '';
    public $newDosage = '';
    public $newFrequencyDuration = '';

    public bool $showMedicalHistoryModal = false;
    public bool $showPreviousConsultationsModal = false;

    public $previousAppointments = [];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment->load([
            'patient.user',
            'patient.bloodType',
            'doctor.user',
            'prescriptions'
        ]);

        $this->date = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
        $this->time = Carbon::parse($appointment->start_time)->format('H:i');
        $this->status = $appointment->status;

        $this->diagnosis = $appointment->diagnosis;
        $this->treatment = $appointment->treatment;
        $this->notes = $appointment->notes;

        $this->prescriptionsList = $appointment->prescriptions
            ->map(function ($prescription) {
                return [
                    'medication' => $prescription->medication,
                    'dosage' => $prescription->dosage,
                    'frequency_duration' => $prescription->frequency_duration,
                ];
            })
            ->toArray();

        $this->generateTimeOptions();
        $this->loadPreviousAppointments();
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

    public function loadPreviousAppointments()
    {
        $this->previousAppointments = Appointment::with(['doctor.user'])
            ->where('patient_id', $this->appointment->patient_id)
            ->where('id', '!=', $this->appointment->id)
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->get();
    }

    public function openMedicalHistory()
    {
        $this->showMedicalHistoryModal = true;
    }

    public function closeMedicalHistory()
    {
        $this->showMedicalHistoryModal = false;
    }

    public function openPreviousConsultations()
    {
        $this->loadPreviousAppointments();
        $this->showPreviousConsultationsModal = true;
    }

    public function closePreviousConsultations()
    {
        $this->showPreviousConsultationsModal = false;
    }

    public function addPrescription()
    {
        $this->validate([
            'newMedication' => 'required|string|max:255',
            'newDosage' => 'required|string|max:255',
            'newFrequencyDuration' => 'required|string|max:255',
        ], [], [
            'newMedication' => 'medicamento',
            'newDosage' => 'dosis',
            'newFrequencyDuration' => 'frecuencia / duración',
        ]);

        $this->prescriptionsList[] = [
            'medication' => $this->newMedication,
            'dosage' => $this->newDosage,
            'frequency_duration' => $this->newFrequencyDuration,
        ];

        $this->reset(['newMedication', 'newDosage', 'newFrequencyDuration']);
    }

    public function removePrescription($index)
    {
        unset($this->prescriptionsList[$index]);
        $this->prescriptionsList = array_values($this->prescriptionsList);
    }

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:scheduled,completed,cancelled',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
            'prescriptionsList.*.medication' => 'required|string|max:255',
            'prescriptionsList.*.dosage' => 'required|string|max:255',
            'prescriptionsList.*.frequency_duration' => 'required|string|max:255',
        ], [], [
            'date' => 'fecha',
            'time' => 'hora',
            'status' => 'estatus',
            'diagnosis' => 'diagnóstico',
            'treatment' => 'tratamiento',
            'notes' => 'notas',
            'prescriptionsList.*.medication' => 'medicamento',
            'prescriptionsList.*.dosage' => 'dosis',
            'prescriptionsList.*.frequency_duration' => 'frecuencia / duración',
        ]);

        $searchDate = Carbon::parse($this->date);
        $searchTime = Carbon::createFromFormat('H:i', $this->time);

        $timeString = $searchTime->format('H:i:s');
        $currentDateString = Carbon::parse($this->appointment->appointment_date)->format('Y-m-d');
        $currentTimeString = Carbon::parse($this->appointment->start_time)->format('H:i:s');

        if ($searchDate->format('Y-m-d') !== $currentDateString || $timeString !== $currentTimeString) {
            if ($searchDate->isToday() && $searchTime->lessThan(now())) {
                session()->flash('error', 'No puedes reprogramar para fechas/horas en el pasado.');
                return;
            }

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
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'notes' => $this->notes,
        ]);

        $this->appointment->prescriptions()->delete();

        foreach ($this->prescriptionsList as $item) {
            $this->appointment->prescriptions()->create([
                'medication' => $item['medication'],
                'dosage' => $item['dosage'],
                'frequency_duration' => $item['frequency_duration'],
            ]);
        }

        session()->flash('success', 'Cita médica actualizada correctamente.');

        return redirect()->route('admin.appointments.index');
    }

    public function render()
    {
        return view('livewire.admin.appointments.edit-appointment');
    }
}
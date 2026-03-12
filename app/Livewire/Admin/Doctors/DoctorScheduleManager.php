<?php

namespace App\Livewire\Admin\Doctors;

use Livewire\Component;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Support\Carbon;
use WireUi\Traits\WireUiActions;

class DoctorScheduleManager extends Component
{
    use WireUiActions;

    public Doctor $doctor;

    // Block A
    public $baseStartTime = '09:00';
    public $baseEndTime = '17:00';

    // Block B - Matrix
    // format slots[day_of_week][time] = boolean
    public $slots = [];
    public $timeLabels = [];
    public $days = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
    ];

    public function mount(Doctor $doctor)
    {
        $this->doctor = $doctor;
        $this->loadExistingSchedules();
    }

    public function loadExistingSchedules()
    {
        $this->timeLabels = [];
        $this->slots = [];

        $schedules = $this->doctor->schedules;
        if($schedules->isNotEmpty()) {
            
            // Generate matrix using the existing schedules min and max, or just fallback to 09:00 - 17:00
            // but for simplicity we will always use baseStartTime and baseEndTime.
            $this->generateMatrix();

            foreach ($schedules as $schedule) {
                // Remove seconds
                $timeKey = Carbon::parse($schedule->start_time)->format('H:i');
                if (isset($this->slots[$schedule->day_of_week][$timeKey])) {
                    $this->slots[$schedule->day_of_week][$timeKey] = true;
                }
            }
        } else {
            $this->generateMatrix();
        }
    }

    public function generateMatrix()
    {
        $this->timeLabels = [];
        $this->slots = [];

        try {
            $start = Carbon::createFromFormat('H:i', $this->baseStartTime);
            $end = Carbon::createFromFormat('H:i', $this->baseEndTime);

            if ($start->greaterThanOrEqualTo($end)) {
                session()->flash('error', 'La hora de inicio debe ser menor a la hora de fin.');
                return;
            }

            $current = $start->copy();
            while ($current->lessThan($end)) {
                $timeStr = $current->format('H:i');
                $this->timeLabels[] = $timeStr;
                foreach ($this->days as $dayId => $dayName) {
                    $this->slots[$dayId][$timeStr] = false;
                }
                $current->addMinutes(15);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Formato de hora inválido.');
        }
    }

    public function saveSchedules()
    {
        // Delete all old schedules
        $this->doctor->schedules()->delete();

        $newSchedules = [];
        $now = now();
        foreach ($this->slots as $dayOfWeek => $times) {
            foreach ($times as $timeKey => $isSelected) {
                if ($isSelected) {
                    $startTime = Carbon::createFromFormat('H:i', $timeKey);
                    $endTime = $startTime->copy()->addMinutes(15);
                    $newSchedules[] = [
                        'doctor_id' => $this->doctor->id,
                        'day_of_week' => $dayOfWeek,
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $endTime->format('H:i:s'),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($newSchedules)) {
            DoctorSchedule::insert($newSchedules);
        }

        session()->flash('success', 'Horarios guardados correctamente.');
    }

    public function render()
    {
        return view('livewire.admin.doctors.doctor-schedule-manager')
            ->layout('layouts.admin', [
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
                    ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
                    ['name' => 'Gestor de Horarios', 'href' => '#'],
                ]
            ]);
    }
}

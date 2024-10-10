<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Leave;
use Auth;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Presensi extends Component
{
    use WithFileUploads;

    public $latitude;
    public $longitude;
    public $insideRadius = false;
    public $showCamera = false;
    public $photo;

    protected $rules = [
        'photo' => 'required|image|max:1024', // max 1MB
        'latitude' => 'required',
        'longitude' => 'required',
    ];

    public function render()
    {
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        $attendance = Attendance::where('user_id', Auth::user()->id)
            ->whereDate('created_at', date('Y-m-d'))->first();
        return view('livewire.presensi', [
            'schedule' => $schedule,
            'insideRadius' => $this->insideRadius,
            'attendance' => $attendance,
        ]);
    }

    public function initiateAttendance()
    {
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($this->insideRadius) {
            $this->showCamera = true;
        } else {
            session()->flash('error', 'Anda berada di luar radius yang diizinkan.');
        }
    }

    public function capturePhoto()
    {
        $this->validate();

        $schedule = Schedule::where('user_id', Auth::user()->id)->first();

        // Cek jika sedang cuti
        $today = Carbon::today()->format('Y-m-d');
        $approveLeave = Leave::where('user_id', Auth::user()->id)
            ->where('status', 'approve')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();

        if ($approveLeave) {
            session()->flash('error', 'Anda sedang cuti');
            return;
        }

        if ($schedule) {
            $attendance = Attendance::where('user_id', Auth::user()->id)
                ->whereDate('created_at', date('Y-m-d'))->first();

            $user = Auth::user();
            $fileName = time() . '.' . $this->photo->getClientOriginalExtension();
            $folderPath = 'attendance/' . $user->name;

            // Ensure the directory exists
            Storage::disk('public')->makeDirectory($folderPath);

            $photoPath = $this->photo->storeAs($folderPath, $fileName, 'public');

            if (!$attendance) {
                // Presensi masuk
                Attendance::create([
                    'user_id' => $user->id,
                    'schedule_latitude' => $schedule->office->latitude,
                    'schedule_longitude' => $schedule->office->longitude,
                    'schedule_waktu_datang' => $schedule->shift->waktu_datang,
                    'schedule_waktu_pulang' => $schedule->shift->waktu_pulang,
                    'datang_latitude' => $this->latitude,
                    'datang_longitude' => $this->longitude,
                    'waktu_datang' => Carbon::now()->toTimeString(),
                    'foto_absen_datang' => $photoPath,
                    'foto_absen_pulang' => null,
                ]);
                session()->flash('message', 'Presensi masuk berhasil.');
            } else {
                // Presensi pulang
                if ($attendance->waktu_pulang) {
                    session()->flash('error', 'Anda sudah melakukan presensi pulang hari ini.');
                    return;
                }
                $attendance->update([
                    'pulang_latitude' => $this->latitude,
                    'pulang_longitude' => $this->longitude,
                    'waktu_pulang' => Carbon::now()->toTimeString(),
                    'foto_absen_pulang' => $photoPath,
                ]);
                session()->flash('message', 'Presensi pulang berhasil.');
            }

            $this->reset(['photo', 'showCamera']);
            return redirect('admin/attendances');
        }
    }
}
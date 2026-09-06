<?php

namespace App\Livewire\Admin;

use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Staff Scheduling'])]
class StaffScheduling extends Component
{
    public string $weekStart = '';
    public bool $showModal = false;
    public ?int $editId = null;

    public ?int $userId = null;
    public string $date = '';
    public string $startTime = '08:00';
    public string $endTime = '16:00';
    public string $role = 'waiter';
    public string $status = 'scheduled';
    public string $notes = '';

    public function mount(): void
    {
        $this->weekStart = Carbon::now()->startOfWeek()->format('Y-m-d');
    }

    public function prevWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->format('Y-m-d');
    }

    public function nextWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->format('Y-m-d');
    }

    public function openCreate(?string $date = null): void
    {
        $this->reset(['userId','startTime','endTime','role','status','notes','editId']);
        $this->date      = $date ?? today()->format('Y-m-d');
        $this->startTime = '08:00';
        $this->endTime   = '16:00';
        $this->role      = 'waiter';
        $this->status    = 'scheduled';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $shift           = Shift::findOrFail($id);
        $this->editId    = $id;
        $this->userId    = $shift->user_id;
        $this->date      = $shift->date->format('Y-m-d');
        $this->startTime = $shift->start_time;
        $this->endTime   = $shift->end_time;
        $this->role      = $shift->role;
        $this->status    = $shift->status;
        $this->notes     = $shift->notes ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'userId'    => 'required|exists:users,id',
            'date'      => 'required|date',
            'startTime' => 'required',
            'endTime'   => 'required|after:startTime',
            'role'      => 'required|in:manager,kitchen,waiter,bar',
        ]);

        $data = [
            'user_id'    => $this->userId,
            'date'       => $this->date,
            'start_time' => $this->startTime,
            'end_time'   => $this->endTime,
            'role'       => $this->role,
            'status'     => $this->status,
            'notes'      => $this->notes ?: null,
        ];

        $this->editId ? Shift::findOrFail($this->editId)->update($data) : Shift::create($data);
        $this->showModal = false;
        session()->flash('success', 'Shift saved.');
    }

    public function updateStatus(int $id, string $status): void
    {
        Shift::findOrFail($id)->update(['status' => $status]);
    }

    public function delete(int $id): void
    {
        Shift::findOrFail($id)->delete();
    }

    public function render()
    {
        $start = Carbon::parse($this->weekStart);
        $end   = $start->copy()->endOfWeek();
        $days  = collect();
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $days->push($d->copy());
        }

        $shifts = Shift::with('user')
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn ($s) => $s->date->format('Y-m-d'));

        $staff = User::whereIn('role', ['manager', 'kitchen', 'waiter', 'bar'])->where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.staff-scheduling', compact('days', 'shifts', 'staff'));
    }
}

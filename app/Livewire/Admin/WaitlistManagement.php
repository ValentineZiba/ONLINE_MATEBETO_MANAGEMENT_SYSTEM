<?php

namespace App\Livewire\Admin;

use App\Models\Waitlist;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Waitlist'])]
class WaitlistManagement extends Component
{
    public string $guestName = '';
    public string $phone = '';
    public string $email = '';
    public int $partySize = 2;
    public string $notes = '';
    public bool $showModal = false;
    public ?int $editId = null;

    public function addGuest(): void
    {
        $this->validate([
            'guestName' => 'required|string|min:2',
            'phone'     => 'required|string|min:7',
            'partySize' => 'required|integer|min:1|max:20',
        ]);

        Waitlist::create([
            'guest_name' => $this->guestName,
            'phone'      => $this->phone,
            'email'      => $this->email ?: null,
            'party_size' => $this->partySize,
            'notes'      => $this->notes ?: null,
            'status'     => 'waiting',
        ]);

        $this->reset(['guestName', 'phone', 'email', 'partySize', 'notes', 'showModal']);
        session()->flash('success', 'Guest added to waitlist.');
    }

    public function notify(int $id): void
    {
        Waitlist::findOrFail($id)->update(['status' => 'notified', 'notified_at' => now()]);
    }

    public function seat(int $id): void
    {
        Waitlist::findOrFail($id)->update(['status' => 'seated', 'seated_at' => now()]);
    }

    public function cancel(int $id): void
    {
        Waitlist::findOrFail($id)->update(['status' => 'cancelled']);
    }

    public function remove(int $id): void
    {
        Waitlist::findOrFail($id)->delete();
    }

    #[Polling('20s')]
    public function render()
    {
        return view('livewire.admin.waitlist-management', [
            'active'   => Waitlist::active()->orderBy('created_at')->get(),
            'seated'   => Waitlist::where('status', 'seated')->latest('seated_at')->limit(20)->get(),
            'waiting'  => Waitlist::where('status', 'waiting')->count(),
            'notified' => Waitlist::where('status', 'notified')->count(),
        ]);
    }
}

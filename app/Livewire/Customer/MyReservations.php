<?php

namespace App\Livewire\Customer;

use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public', ['title' => 'My Reservations'])]
class MyReservations extends Component
{
    use WithPagination;

    public function cancel(int $id): void
    {
        Reservation::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id)
            ->update(['status' => 'cancelled']);
    }

    public function render()
    {
        return view('livewire.customer.my-reservations', [
            'reservations' => Reservation::where('user_id', auth()->id())
                ->with('table')
                ->orderByDesc('reservation_date')
                ->paginate(10),
        ]);
    }
}

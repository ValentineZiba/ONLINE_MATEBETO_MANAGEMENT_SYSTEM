<?php

namespace App\Livewire\Admin;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin', ['title' => 'Reservations'])]
class ReservationManagement extends Component
{
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $statusFilter = '';
    #[Url] public string $dateFilter = '';

    public bool $showModal = false;
    public ?Reservation $selectedReservation = null;
    public ?int $assignTableId = null;

    public function mount(): void
    {
        $this->dateFilter = today()->format('Y-m-d');
    }

    public function viewReservation(int $id): void
    {
        $this->selectedReservation = Reservation::with('table', 'user')->findOrFail($id);
        $this->assignTableId = $this->selectedReservation->table_id;
        $this->showModal = true;
    }

    public function updateStatus(int $id, string $status): void
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => $status]);

        if ($status === 'seated' && $reservation->table_id) {
            $reservation->table->update(['status' => 'occupied']);
        }
        if (in_array($status, ['completed', 'cancelled', 'no_show']) && $reservation->table_id) {
            $reservation->table->update(['status' => 'available']);
        }

        if ($this->selectedReservation?->id === $id) {
            $this->selectedReservation = $reservation->fresh('table', 'user');
        }
    }

    public function assignTable(): void
    {
        if ($this->selectedReservation && $this->assignTableId) {
            $this->selectedReservation->update(['table_id' => $this->assignTableId]);
            $this->selectedReservation = $this->selectedReservation->fresh('table');
        }
    }

    public function render()
    {
        $reservations = Reservation::with(['table', 'user'])
            ->when($this->search, fn ($q) => $q->where('guest_name', 'like', "%{$this->search}%")
                ->orWhere('guest_email', 'like', "%{$this->search}%")
                ->orWhere('confirmation_code', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFilter, fn ($q) => $q->whereDate('reservation_date', $this->dateFilter))
            ->orderBy('reservation_date')->orderBy('reservation_time')
            ->paginate(15);

        $stats = [
            'today_total' => Reservation::today()->count(),
            'pending' => Reservation::where('status', 'pending')->count(),
            'confirmed' => Reservation::today()->where('status', 'confirmed')->count(),
            'seated' => Reservation::today()->where('status', 'seated')->count(),
        ];

        return view('livewire.admin.reservation-management', [
            'reservations' => $reservations,
            'tables' => RestaurantTable::where('status', '!=', 'inactive')->orderBy('number')->get(),
            'stats' => $stats,
        ]);
    }
}

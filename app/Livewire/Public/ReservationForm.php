<?php

namespace App\Livewire\Public;

use App\Mail\ReservationConfirmationMail;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public', ['title' => 'Make a Reservation'])]
class ReservationForm extends Component
{
    public string $guestName = '';
    public string $guestEmail = '';
    public string $guestPhone = '';
    public int $partySize = 2;
    public string $reservationDate = '';
    public string $reservationTime = '';
    public string $occasion = '';
    public string $specialRequests = '';
    public bool $submitted = false;
    public ?string $confirmationCode = null;

    public function mount(): void
    {
        if (auth()->check()) {
            $this->guestName = auth()->user()->name;
            $this->guestEmail = auth()->user()->email;
            $this->guestPhone = auth()->user()->phone ?? '';
        }
        $this->reservationDate = now()->addDay()->format('Y-m-d');
        $this->reservationTime = '19:00';
    }

    public function submit(): void
    {
        $this->validate([
            'guestName' => 'required|string|min:2',
            'guestEmail' => 'required|email',
            'guestPhone' => 'required|string|min:10',
            'partySize' => 'required|integer|min:1|max:20',
            'reservationDate' => 'required|date|after:today',
            'reservationTime' => 'required',
        ]);

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'guest_name' => $this->guestName,
            'guest_email' => $this->guestEmail,
            'guest_phone' => $this->guestPhone,
            'party_size' => $this->partySize,
            'reservation_date' => $this->reservationDate,
            'reservation_time' => $this->reservationTime,
            'occasion' => $this->occasion ?: null,
            'special_requests' => $this->specialRequests ?: null,
            'status' => 'pending',
        ]);

        $this->confirmationCode = $reservation->confirmation_code;
        $this->submitted = true;

        Mail::to($this->guestEmail)->send(new ReservationConfirmationMail($reservation));
    }

    public function getAvailableTimesProperty(): array
    {
        $times = [];
        for ($h = 7; $h <= 22; $h++) {
            foreach (['00', '30'] as $m) {
                $times[] = sprintf('%02d:%s', $h, $m);
            }
        }
        return $times;
    }

    public function render()
    {
        return view('livewire.public.reservation-form');
    }
}

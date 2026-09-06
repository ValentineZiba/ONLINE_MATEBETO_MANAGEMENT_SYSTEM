<?php

namespace App\Livewire\Admin;

use App\Models\PaymentTransaction;
use App\Services\Payments\PaymentManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin', ['title' => 'Payments'])]
class PaymentTransactions extends Component
{
    use WithPagination;

    #[Url] public string $gatewayFilter = '';
    #[Url] public string $statusFilter = '';
    #[Url] public string $search = '';

    public ?int $viewingId = null;

    public function updatingGatewayFilter(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }

    public function view(int $id): void
    {
        $this->viewingId = $id;
    }

    public function closeView(): void
    {
        $this->viewingId = null;
    }

    public function verifyNow(int $id): void
    {
        $transaction = PaymentTransaction::findOrFail($id);
        $manager = app(PaymentManager::class);
        $gateway = $manager->gateway($transaction->gateway);
        $result = $gateway->query($transaction);

        match ($result->status) {
            'succeeded' => $manager->markSucceeded($transaction, $result->rawResponse, $result->gatewayReference),
            'failed' => $manager->markFailed($transaction, $result->failureReason ?? 'Verification failed', $result->rawResponse),
            default => null,
        };

        session()->flash('success', "Transaction {$transaction->reference} re-checked — status: {$result->status}.");
    }

    public function render()
    {
        $transactions = PaymentTransaction::with(['transactionable', 'initiator'])
            ->when($this->gatewayFilter, fn ($q) => $q->where('gateway', $this->gatewayFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn ($q) => $q->where('reference', 'like', "%{$this->search}%"))
            ->latest('id')
            ->paginate(20);

        return view('livewire.admin.payment-transactions', [
            'transactions' => $transactions,
            'viewing' => $this->viewingId ? PaymentTransaction::with(['transactionable', 'initiator'])->find($this->viewingId) : null,
        ]);
    }
}

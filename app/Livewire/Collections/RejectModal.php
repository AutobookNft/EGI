<?php

namespace App\Livewire\Collections;

use App\Models\WalletChangeApprovalModel;
use Livewire\Component;
use App\Models\WalletChangeApproval;

class RejectModal extends Component
{
    public $approvalId;
    public $rejectionReason;

    protected $listeners = ['openRejectModal'];

    public function openRejectModal($approvalId)
    {
        $this->approvalId = $approvalId;
        $this->rejectionReason = '';
    }

    public function reject()
    {
        $approval = WalletChangeApprovalModel::findOrFail($this->approvalId);

        $approval->wallet->update(json_decode($approval->wallet->previous_values, true));
        $approval->wallet->update(['approval' => 'approved']);
        $approval->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'approved_at' => now(),
        ]);

        session()->flash('message', 'Modifica rifiutata.');
        $this->emit('changesRejected');
    }

    public function render()
    {
        return view('livewire.collections.reject-modal');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamUser extends Pivot
{
    protected $table = 'team_user';

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'wallet',
        'royalty_mint',
        'royalty_rebind',
        'approval',
        'previous_values',
        'status',
    ];

    public function updateWithApproval(array $data, $userId)
    {
        // Backup dei vecchi valori
        $this->previous_values = $this->only(array_keys($data));

        // Imposta lo stato su pending e salva i nuovi valori
        $this->fill($data);
        $this->approval = 'pending';
        $this->save();

        // Logica per tracciare la modifica in una tabella di approvazione separata
        WalletChangeApproval::create([
            'wallet_id' => $this->id,
            'requested_by_user_id' => $userId,
            'approver_user_id' => $this->team->creator_id,
            'change_type' => 'update',
            'change_details' => json_encode($data),
            'status' => 'pending',
        ]);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

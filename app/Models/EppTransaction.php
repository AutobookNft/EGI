<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * EPP Transaction model.
 *
 * Represents a financial contribution to an Environment Protection Program (EPP)
 * from EGI sales or resales. Each transaction record tracks the amount,
 * source, and allocation to specific environmental initiatives.
 *
 * --- Core Logic ---
 * 1. Records financial contributions to environmental projects
 * 2. Tracks the source of funds (minting or resale transactions)
 * 3. Links transactions to specific EPPs and EGIs
 * 4. Provides audit trail for transparency
 * 5. Supports impact calculation and reporting
 * --- End Core Logic ---
 *
 * @package App\Models
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @property int $id
 * @property int $epp_id
 * @property int|null $egi_id
 * @property int|null $collection_id
 * @property string $transaction_type mint, rebind
 * @property float $amount
 * @property string|null $blockchain_tx_id
 * @property string|null $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EppTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'epp_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'epp_id',
        'egi_id',
        'collection_id',
        'transaction_type',
        'amount',
        'blockchain_tx_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * Get the EPP that received this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function epp()
    {
        return $this->belongsTo(Epp::class, 'epp_id');
    }

    /**
     * Get the EGI associated with this transaction, if any.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function egi()
    {
        return $this->belongsTo(Egi::class, 'egi_id');
    }

    /**
     * Get the collection associated with this transaction, if any.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    /**
     * Scope a query to only include mint transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMint($query)
    {
        return $query->where('transaction_type', 'mint');
    }

    /**
     * Scope a query to only include rebind (resale) transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRebind($query)
    {
        return $query->where('transaction_type', 'rebind');
    }

    /**
     * Scope a query to only include transactions for a specific EPP.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $eppId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEpp($query, $eppId)
    {
        return $query->where('epp_id', $eppId);
    }

    /**
     * Scope a query to only include successful transactions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Create a new transaction from an EGI mint or rebind.
     *
     * @param int $eppId
     * @param int|null $egiId
     * @param int|null $collectionId
     * @param string $type
     * @param float $amount
     * @param string|null $blockchainTxId
     * @return self
     */
    public static function createFromEgiTransaction($eppId, $egiId, $collectionId, $type, $amount, $blockchainTxId = null)
    {
        $transaction = new self();
        $transaction->epp_id = $eppId;
        $transaction->egi_id = $egiId;
        $transaction->collection_id = $collectionId;
        $transaction->transaction_type = $type;
        $transaction->amount = $amount;
        $transaction->blockchain_tx_id = $blockchainTxId;
        $transaction->status = $blockchainTxId ? 'confirmed' : 'pending';
        $transaction->save();

        // Update the EPP's total funds
        if ($transaction->status === 'confirmed') {
            $epp = Epp::find($eppId);
            $epp->total_funds += $amount;
            $epp->save();
        }

        return $transaction;
    }
}

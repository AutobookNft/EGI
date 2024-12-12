<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarContext extends Model
{
    protected $primaryKey = 'context';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['context'];

    /**
     * Relazione con BarContextMenu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function summaries()
    {
        return $this->hasMany(BarContextSummarie::class, 'context', 'context');
    }
}

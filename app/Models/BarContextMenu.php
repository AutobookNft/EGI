<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarContextMenu extends Model
{
    protected $fillable = ['context', 'summary', 'name', 'route', 'icon', 'permission'];

    /**
     * Relazione con BarContext.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function summary()
    {
        return $this->belongsTo(BarContextSummarie::class, 'summary')->orderBy('position', 'asc');
    }
}

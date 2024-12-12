<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarContextSummarie extends Model
{

    protected $primaryKey = 'summary';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['context', 'summary', 'permission', 'tip', 'route', 'icon'];

    public function menus()
    {
        return $this->hasMany(BarContextMenu::class, 'summary')->orderBy('position', 'asc');
    }

    public function context()
    {
        return $this->belongsTo(BarContext::class, 'context', 'context');
    }
}


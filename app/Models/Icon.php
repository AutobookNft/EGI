<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'style',
        'type',
        'class',
        'html',
    ];

    /**
     * Accessor to get the full HTML for the icon.
     *
     * @return string
     */
    public function getFullHtmlAttribute(): string
    {
        return $this->html;
    }

    /**
     * Mutator to sanitize the HTML input.
     *
     * @param string $value
     */
    public function setHtmlAttribute(string $value): void
    {
        // Optional sanitization logic if needed
        $this->attributes['html'] = trim($value);
    }
}

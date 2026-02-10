<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
}

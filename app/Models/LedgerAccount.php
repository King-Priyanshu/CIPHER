<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'currency',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
}

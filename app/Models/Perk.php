<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perk extends Model
{
    protected $fillable = [
        'membership_card_id',
        'perk_type',
        'discount_percentage',
        'description',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function membershipCard()
    {
        return $this->belongsTo(MembershipCard::class);
    }
}

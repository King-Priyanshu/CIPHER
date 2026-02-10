<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\MembershipCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MembershipCardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Find existing card or create one
        $card = MembershipCard::firstOrCreate(
            ['user_id' => $user->id],
            [
                'card_number' => $this->generateCardNumber(),
                'status' => 'active',
                'issued_at' => now(),
            ]
        );

        // Ensure default perks exist (Mocking/Seeding logic for now)
        if ($card->perks()->count() === 0) {
            $this->seedDefaultPerks($card);
        }

        return view('subscriber.card.show', compact('card', 'user'));
    }

    protected function generateCardNumber()
    {
        // Format: CIPHER-YEAR-RANDOM (e.g., CIPHER-2026-X8Y2)
        // Or simple 16 digit: 4000 1234 5678 9010
        // Let's go with a premium format
        return 'CPHR-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
    }

    protected function seedDefaultPerks(MembershipCard $card)
    {
        $card->perks()->createMany([
            [
                'perk_type' => 'food',
                'discount_percentage' => 15.00,
                'description' => '15% off at Partner Restaurants',
                'is_active' => true,
            ],
            [
                'perk_type' => 'medical',
                'discount_percentage' => 10.00,
                'description' => '10% off at C-Hospitals',
                'is_active' => true,
            ],
            [
                'perk_type' => 'shopping',
                'discount_percentage' => 5.00,
                'description' => '5% Cashback on Community Stores',
                'is_active' => true,
            ],
        ]);
    }
}

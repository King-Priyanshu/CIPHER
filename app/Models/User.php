<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'terms_accepted_at',
        'razorpay_customer_id',
        'stripe_customer_id',
        'phone',
        'participation_mode',
        'referral_code',
        'referred_by',
        'payment_reminders_enabled',
        'payment_reminder_method',
        'payment_reminder_days',
        'two_factor_enabled',
        'two_factor_method',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get roles as a collection (for compatibility with views using plural).
     */
    public function getRolesAttribute()
    {
        return $this->role ? collect([$this->role]) : collect();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $slug): bool
    {
        return $this->role && $this->role->slug === $slug;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a subscriber.
     */
    public function isSubscriber(): bool
    {
        return $this->hasRole('subscriber');
    }

    /**
     * Check if user has accepted terms.
     */
    public function hasAcceptedTerms(): bool
    {
        return $this->terms_accepted_at !== null;
    }

    public function subscription()
    {
        return $this->hasOne(UserSubscription::class)->latestOfMany();
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get all project investments made by this user.
     */
    public function investments()
    {
        return $this->hasMany(ProjectInvestment::class);
    }

    /**
     * Get all profit logs for this user.
     */
    public function profitLogs()
    {
        return $this->hasMany(UserProfitLog::class);
    }

    /**
     * The user who referred this user.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Users referred by this user.
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * Boot method to auto-generate referral code.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(\Illuminate\Support\Str::random(8));
            }
        });
    }

    /**
     * Get the user's membership card.
     */
    public function membershipCard()
    {
        return $this->hasOne(MembershipCard::class);
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        $subscription = $this->subscription;

        if (!$subscription) {
            return false;
        }

        return $subscription->status === 'active' &&
            ($subscription->ends_at === null || $subscription->ends_at->isFuture());
    }

    /**
     * Get all SIPs associated with the user.
     */
    public function sips()
    {
        return $this->hasMany(Sip::class);
    }

    /**
     * Get all refund requests made by the user.
     */
    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Get the user's wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get user's wallet balance.
     */
    public function getWalletBalanceAttribute(): float
    {
        return (float) ($this->wallet?->balance ?? 0.0);
    }

    /**
     * Get all wallet transactions for the user.
     */
    public function walletTransactions()
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class);
    }
}

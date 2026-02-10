<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'entity_type',
        'entity_id',
        'ip_address',
        'user_agent',
        'severity',
        'admin_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related entity (polymorphic).
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by entity type.
     */
    public function scopeForEntity($query, string $type, ?int $id = null)
    {
        $query->where('entity_type', $type);
        if ($id) {
            $query->where('entity_id', $id);
        }
        return $query;
    }
}

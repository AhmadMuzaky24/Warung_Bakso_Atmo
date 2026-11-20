<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CateringOrder extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_VERIFIED = 'verified';
    public const PAYMENT_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_SUBMITTED,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETED,
        self::STATUS_REJECTED,
    ];

    public const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING,
        self::PAYMENT_VERIFIED,
        self::PAYMENT_REJECTED,
    ];

    protected $fillable = [
        'booking_code',
        'user_id',
        'event_date',
        'event_address',
        'customer_phone',
        'notes',
        'total_amount',
        'dp_amount',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_amount' => 'integer',
        'dp_amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CateringOrderItem::class);
    }
}

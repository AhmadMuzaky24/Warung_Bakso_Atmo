<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public const STATUS_LABELS = [
        self::STATUS_SUBMITTED => 'Diajukan',
        self::STATUS_PROCESSING => 'Diproses',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    public const PAYMENT_STATUS_LABELS = [
        self::PAYMENT_PENDING => 'Menunggu verifikasi',
        self::PAYMENT_VERIFIED => 'Terverifikasi',
        self::PAYMENT_REJECTED => 'Ditolak',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_STATUS_LABELS[$this->payment_status] ?? ucfirst((string) $this->payment_status);
    }

    /**
     * Filter daftar pesanan untuk halaman operasional (Staff) dan verifikasi (Admin).
     *
     * Kunci yang dipakai: search (kode booking / nama pelanggan), status,
     * payment_status, start_date, end_date (rentang tanggal acara, Y-m-d).
     * Nilai yang tidak valid diabaikan supaya URL manipulatif tidak menimbulkan error.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $text = fn (string $key): string => is_string($filters[$key] ?? null) ? trim($filters[$key]) : '';

        $date = function (string $key) use ($text): ?string {
            $value = $text($key);

            return $value !== '' && Carbon::canBeCreatedFromFormat($value, 'Y-m-d') ? $value : null;
        };

        $search = $text('search');
        $status = $text('status');
        $paymentStatus = $text('payment_status');
        $startDate = $date('start_date');
        $endDate = $date('end_date');

        return $query
            ->when($search !== '', function (Builder $q) use ($search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('booking_code', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $uq) => $uq->where('name', 'like', "%{$search}%"));
                });
            })
            ->when(in_array($status, self::STATUSES, true), fn (Builder $q) => $q->where('status', $status))
            ->when(
                in_array($paymentStatus, self::PAYMENT_STATUSES, true),
                fn (Builder $q) => $q->where('payment_status', $paymentStatus)
            )
            ->when($startDate, fn (Builder $q) => $q->whereDate('event_date', '>=', $startDate))
            ->when($endDate, fn (Builder $q) => $q->whereDate('event_date', '<=', $endDate));
    }
}

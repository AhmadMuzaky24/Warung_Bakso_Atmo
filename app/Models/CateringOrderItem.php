<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'catering_order_id',
        'menu_id',
        'menu_name',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'quantity' => 'integer',
        'subtotal' => 'integer',
    ];

    public function cateringOrder(): BelongsTo
    {
        return $this->belongsTo(CateringOrder::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}

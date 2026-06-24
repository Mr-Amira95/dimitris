<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int         $id
 * @property string|null $order_number
 * @property int         $customer_id
 * @property int|null    $customer_phone_id
 * @property int|null    $customer_address_id
 * @property string      $order_date
 * @property string|null $preferred_delivery_date
 * @property string|null $preferred_delivery_time
 * @property string|null $internal_notes
 * @property string      $status
 * @property int         $created_by
 * @property int|null    $driver_user_id
 * @property string|null $outsourced_driver_name
 * @property float|null  $outsourced_delivery_cost
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property \Illuminate\Support\Carbon|null $archived_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'customer_phone_id', 'customer_address_id',
        'order_date', 'preferred_delivery_date', 'preferred_delivery_time',
        'internal_notes', 'status', 'created_by', 'archived_at',
        'driver_user_id', 'outsourced_driver_name', 'outsourced_delivery_cost', 'dispatched_at',
    ];

    protected $casts = [
        'order_date'               => 'date',
        'preferred_delivery_date'  => 'date',
        'archived_at'              => 'datetime',
        'dispatched_at'            => 'datetime',
        'outsourced_delivery_cost' => 'decimal:3',
    ];

    public const STATUS_LABELS = [
        'new'       => 'New Order',
        'packing'   => 'Preparation & Packing',
        'dispatch'  => 'Dispatch',
        'picked_up' => 'Picked Up',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    protected static function booted(): void
    {
        static::created(function (Order $order) {
            $order->updateQuietly([
                'order_number' => \sprintf('ORD-%05d', $order->id),
            ]);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerPhone(): BelongsTo
    {
        return $this->belongsTo(CustomerPhone::class, 'customer_phone_id');
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class)->orderByDesc('created_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getIsOutsourcedAttribute(): bool
    {
        $name = $this->getAttribute('outsourced_driver_name');
        return $name !== null && $name !== '';
    }
}

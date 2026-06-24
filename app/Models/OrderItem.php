<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'filling_id', 'grind_id', 'qty'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function filling(): BelongsTo
    {
        return $this->belongsTo(Filling::class);
    }

    public function grind(): BelongsTo
    {
        return $this->belongsTo(Grind::class);
    }
}

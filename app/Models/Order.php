<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable  = [
        'total',
        'kargo_price',
    ];
    public function products()
    {
        return $this->HasMany(OrderProduct::class, 'order_id', 'id');
    }
}

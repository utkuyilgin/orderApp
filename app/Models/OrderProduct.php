<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable  = [
        'product_id',
        'order_id',
        'quantity',
        'discount_price',
        'list_price',
        'campaign_name',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}

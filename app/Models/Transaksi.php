<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    
    protected $fillable = [
        'user_id',
        'product_id',
        'name',
        'phone',
        'shipping_address',
        'total_price',
        'status',
        'payment_status',
        'bukti_pembayaran'
    ];

    protected $casts = [
        'status' => 'string',
        'payment_status' => 'string'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
} 
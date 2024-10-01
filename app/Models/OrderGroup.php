<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderGroup extends Model
{
    protected $fillable = ['payment_method', 'e_receipt', 'status', 'customer_name'];

    // An OrderGroup has many Orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'order_group_id');
    }
}


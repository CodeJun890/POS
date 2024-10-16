<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderGroup extends Model
{
    protected $fillable = ['payment_method', 'e_receipt', 'status', 'customer_name', 'branch_id']; // Add branch_id to fillable

    // An OrderGroup has many Orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'order_group_id');
    }

    // Relationship to the Branch model
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

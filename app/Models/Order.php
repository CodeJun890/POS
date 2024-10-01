<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['item', 'quantity', 'sauce', 'image', 'price', 'profit', 'order_group_id'];

    // Define the relationship to the OrderGroup model
    public function orderGroup()
    {
        return $this->belongsTo(OrderGroup::class, 'order_group_id');
    }

    /**
     * Boot method to automatically set profit when an Order is created.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Automatically set the profit when an order is created
            $order->profit = $order->getProfitByItem($order->item);
        });
    }

    /**
     * Calculate profit based on the item name.
     *
     * @param string $itemName
     * @return float|null
     */
    protected function getProfitByItem($itemName)
    {
        // Define the mapping of items to their profit values
        $profitMapping = [
            'Buy 1 Take 1 Sliders' => 21.22,
            'Manila Burger' => 25.85,
            'New York Burger' => 39.80,
            'Berlin Burger Steak' => 30.53,
            'French Fries (Solo)' => 21.53,  // Corrected naming
            'French Fries (Barkada)' => 29.85,  // Updated key
            'Water' => 13.00,
            'Coke' => 14.00,
            'Sprite' => 14.00,
            'Royal' => 14.00,
            'Mountain Dew' => 14.00,
        ];

        // Return the profit value based on the item name, or 0 if not found
        return $profitMapping[$itemName] ?? 0;
    }

}

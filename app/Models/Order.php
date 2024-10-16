<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['item', 'quantity', 'sauce', 'image', 'price', 'profit', 'order_group_id', 'branch_id'];

    // Define the relationship to the OrderGroup model
    public function orderGroup()
    {
        return $this->belongsTo(OrderGroup::class, 'order_group_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
            'Buy 1 Take 1 Sliders' => 16.58,
            'Manila Burger' => 19.16,
            'New York Burger' => 27.96,
            'Berlin Burger Steak' => 22.28,
            'French Fries (Solo)' => 21.28,  // Corrected naming
            'French Fries (Barkada)' => 29.60,  // Updated key
            'Water (CvSU)' => 7,
            'Water' => 16,
            'Coke' => 13.50,
            'Sprite' => 13.50,
            'Royal' => 13.50,
            'Mountain Dew' => 13.50,
            'Rice' => 5,
            'Egg' => 5,
            'Lettuce' => 5,
            'Tomato' => 5,
            'Garlic Mayo' => 5,
            'Garlic BBQ' => 5,
            'Kebab' => 5,
            'Yangnyeom' => 5,
            'Cheese Sauce' => 5,
            'Hot Sauce' => 5,
        ];

        // Return the profit value based on the item name, or 0 if not found
        return $profitMapping[$itemName] ?? 0;
    }

}

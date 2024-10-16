<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable  = ['item_name', 'item_quantity', 'item_image'];

    // Function to add stock
    public function addStock($quantity)
    {
        $this->increment('item_quantity', $quantity);
    }

    // Function to deduct stock
    public function deductStock($quantity)
    {
        if ($this->item_quantity >= $quantity) {
            $this->decrement('item_quantity', $quantity);
        } else {
            throw new \Exception("Not enough stock available");
        }
    }

    // Check if the stock is available
    public function hasStock($quantity)
    {
        return $this->item_quantity >= $quantity;
    }
}

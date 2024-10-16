<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    // Define fillable fields
    protected $fillable = ['name', 'address', 'image'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}

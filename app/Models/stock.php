<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'brand',
        'model',
        'category',
        'quantity',
        'section', // Ensure section is added
    ];

    // Optional: If you have a method to check for low stock
    public function isLowStock()
    {
        return $this->quantity < 5; // Example: Mark items with less than 5 quantity as low stock
    }
}

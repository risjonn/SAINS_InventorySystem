<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'type',  // Can be 'created', 'updated', 'deleted'
    ];
}

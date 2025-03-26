<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'division_id',
        'remarks',
        'status',
        'product_code',
        'product_id',
        'requisition_no'
        'brand',         // New field for brand
        'product_code',  // New field for product code
        'requisition_no', // New field for requisition number
        'erp_number'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'serial_number', 'serial_number');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

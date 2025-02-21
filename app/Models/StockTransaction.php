<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;
    
    const CREATED_AT = 'transaction_date';
    const UPDATED_AT = 'transaction_date';

    protected $fillable = [
        'serial_number', 
        'stock_model', 
        'category', 
        'type', 
        'handled_by', 
        'remarks', 
        'transaction_date',
        'brand', 
        'product_code', 
        'requisition_no', 
        
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'serial_number', 'serial_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}

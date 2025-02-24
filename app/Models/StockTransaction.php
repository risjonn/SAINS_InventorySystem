<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    // We override the default timestamps to use 'transaction_date'
    const CREATED_AT = 'transaction_date';
    const UPDATED_AT = 'transaction_date';

  
    protected $fillable = [
        'serial_number',
        'category',
        'brand',
        'product_code',
        'requisition_no',
        'stock_model',
        'type',
        'handled_by',
        'remarks',
        'transaction_date',
    ];

    /**
     * Relationship to the Stock model, matching 'serial_number'.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class, 'serial_number', 'serial_number');
    }

    /**
     * Relationship to the User model (who handled this transaction).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}

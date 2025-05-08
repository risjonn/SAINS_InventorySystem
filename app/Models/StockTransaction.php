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
        'product_code',
        'product_id',
        'requisition_no',
        'type',
        'handled_by',
        'remarks',
        'transaction_date',
        'manual_date',
        'delivery_note',
        'document_number',
        'name',
        'ticket_number',
        'manual_requisition',
        'loan_start_date',
        'due_date',
        'loan_period',
        'taken_by', 
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'serial_number', 'serial_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

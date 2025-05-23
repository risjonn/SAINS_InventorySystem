<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockTransaction;


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
        'requisition_no',
        'erp_number',
        'sla_category',
        'manual_date',
        'delivery_note',
        'document_number',
        'name',
        'ticket_number',
        'manual_requisition',
        'loan_period',
        'loan_start_date',
        'due_date',
        'sla',
        'quantity'
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
    public function latestTransaction()
    {
        return $this->hasOne(StockTransaction::class, 'serial_number', 'serial_number')
                    ->latestOfMany('transaction_date');
    }
    public function latestLoan()
{
    return $this->hasOne(StockTransaction::class)
                ->where('type', 'LOAN')
                ->latest('transaction_date');
}
}

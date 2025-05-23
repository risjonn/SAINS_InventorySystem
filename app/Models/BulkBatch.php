<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'division_id',
        'initial_quantity',
        'remaining_quantity',
        'received_at',
        'manual_requisition',
        'collection_note',
        'ticket_number',
        'remarks',
        'sla_category',
        'sla'
    ];

    // Make sure dates are properly handled
    protected $dates = [
        'received_at',
        'created_at',
        'updated_at'
    ];

    // Ensure product relationship is established
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Ensure division relationship is established
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Add transactions relationship
    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'bulk_batch_id');
    }
}
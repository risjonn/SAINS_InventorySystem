<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number', 'type', 'handled_by', 'remarks', 'transaction_date'
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
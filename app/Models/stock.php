<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockUpdate;

class Stock extends Model
{
    use HasFactory;

    const LOW_STOCK_TRESHOLD = 10;

    protected $fillable = [
        'serial_number',
        'brand',
        'model',
        'category',
        'quantity',
    ];

    public function isLowStock() {
        return $this->quantity < self::LOW_STOCK_TRESHOLD;
    }

    public function scopeLowStock($query) {
        return $query->where('quantity', '<', self::LOW_STOCK_TRESHOLD);
    }

    public function scopeRecentUpdates ($query) {
        return $query->where('updated_at', '>', now()->subDays(1));
    }

    public function updates()
    {
        return $this->hasMany(StockUpdate::class);
    }
}

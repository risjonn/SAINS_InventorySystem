<?php

namespace App\Observers;

use App\Models\Stock;
use App\Models\StockUpdate;  // Ensure this is imported
use Illuminate\Support\Facades\Log;

class StockObserver
{
    public function created(Stock $stock)
    {
        // Track the creation of a stock
        StockUpdate::create([
            'stock_id' => $stock->id,
            'type' => 'created',
        ]);
    }

    public function updated(Stock $stock)
    {
        // Track the update of a stock
        StockUpdate::create([
            'stock_id' => $stock->id,
            'type' => 'updated',
        ]);
    }

    public function deleting(Stock $stock)
    {
        // Track the deletion of a stock
        StockUpdate::create([
            'stock_id' => $stock->id,
            'type' => 'deleted',
        ]);
    }
}

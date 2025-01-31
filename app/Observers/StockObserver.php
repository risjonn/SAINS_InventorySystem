<?php

namespace App\Observers;

use App\Models\Stock;
use App\Models\StockUpdate;
use Illuminate\Support\Facades\Log;

class StockObserver
{
    public function created(Stock $stock)
    {
        StockUpdate::create([
            'stock_id' => $stock->id,
            'type' => 'created'
        ]);
    }

    public function updated(Stock $stock)
    {
        StockUpdate::create([
            'stock_id' => $stock->id,
            'type' => 'updated'
        ]);
    }

    public function deleting(Stock $stock)
    {
        StockUpdate::create([
            'stock_id' => $stock->id,
            'type' => 'deleted'
        ]);
    }

    public function restored(Stock $stock)
    {
        //
    }

    public function forceDeleted(Stock $stock)
    {
        //
    }
}

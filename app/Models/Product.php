<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserProductThreshold;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code', 'category', 'brand', 'model', 'status'
    ];

    public function threshold()
    {
        return $this->hasOne(UserProductThreshold::class);
    }

    public function statusFlag(int $count): string
    {
        $low  = 30;
        $crit = 20;

        if ($count <= $crit) return 'critical';
        if ($count <= $low) return 'low';
        
        return 'ok';
    }
}

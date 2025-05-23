<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'is_bulk',
    ];

public static function getBulkList()
    {
        return self::where('is_bulk', true)->pluck('name')->map(function($name) {
            return strtolower($name);
        })->toArray();
    }

}
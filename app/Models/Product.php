<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'variant_sku';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'variant_sku', 'name', 'size', 'color', 'unit', 'sales_price', 'cost', 'default_vendor'
    ];

    public function stocks()
    {
        return $this->hasMany(InventoryStock::class, 'variant_sku', 'variant_sku');
    }
}

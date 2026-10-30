<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderLine extends Model
{
    protected $table = 'sales_order_lines';

    protected $fillable = [
        'sales_order_id', 'variant_sku', 'qty', 'unit_price',
        'discount_percent', 'tax_rate', 'subtotal'
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'variant_sku', 'variant_sku');
    }
}

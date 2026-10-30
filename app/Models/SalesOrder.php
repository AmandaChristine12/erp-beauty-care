<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'sales_orders';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'customer_id', 'warehouse_code', 'status', 'date',
        'amount_untaxed', 'amount_tax', 'amount_total',
        'is_delivered', 'is_invoiced'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function lines()
    {
        return $this->hasMany(SalesOrderLine::class, 'sales_order_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_code', 'location_code');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    protected $table = 'warehouses_locations';
    protected $primaryKey = 'location_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'location_code', 'location_name', 'city', 'type'
    ];
}

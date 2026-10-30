<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $table = 'contacts_customers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'type', 'city', 'credit_limit', 'payment_terms', 'opening_receivable'
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'customer_id', 'id');
    }

    /**
     * Hitung Credit Exposure Kumulatif (SR-08 / F-11)
     * Rumus: Unpaid AR + Unbilled Confirmed SO
     */
    public function getCreditExposure()
    {
        // 1. Unpaid AR = Opening AR + Unpaid Posted Customer Invoices
        $unpaidAr = (float) $this->opening_receivable;
        $invoices = DB::table('customer_invoices')
            ->where('customer_id', $this->id)
            ->where('status', 'Posted')
            ->get();
        foreach ($invoices as $inv) {
            $unpaidAr += ((float) $inv->amount_total - (float) $inv->amount_paid);
        }

        // 2. Unbilled Confirmed Orders = Nilai SO status Confirmed yang belum difakturkan
        $unbilledSo = (float) DB::table('sales_orders')
            ->where('customer_id', $this->id)
            ->where('status', 'Confirmed')
            ->where('is_invoiced', false)
            ->sum('amount_total');

        $totalExposure = $unpaidAr + $unbilledSo;
        $creditLimit = (float) $this->credit_limit;
        $remainingCredit = $creditLimit - $totalExposure;
        $isExceeded = $totalExposure > $creditLimit;

        return [
            'unpaid_ar' => $unpaidAr,
            'unbilled_so' => $unbilledSo,
            'total_exposure' => $totalExposure,
            'credit_limit' => $creditLimit,
            'remaining_credit' => $remainingCredit,
            'is_exceeded' => $isExceeded
        ];
    }
}

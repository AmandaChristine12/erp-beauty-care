<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use App\Models\WarehouseLocation;
use App\Models\InventoryStock;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SalesController extends Controller
{
    /**
     * S-01 / List View: Menampilkan seluruh Sales Order & Quotation
     */
    public function index()
    {
        $orders = SalesOrder::with(['customer', 'lines.product'])->orderBy('id', 'desc')->get();
        return view('sales.index', compact('orders'));
    }

    /**
     * S-02 / F-03: Form Customer dan Credit Terms
     */
    public function customers()
    {
        $customers = Customer::all();
        $customerExposures = [];

        foreach ($customers as $c) {
            $customerExposures[$c->id] = $c->getCreditExposure();
        }

        return view('sales.customers', compact('customers', 'customerExposures'));
    }

    /**
     * S-02 Detail: Profil Pelanggan & Uji Credit Exposure (T-03 & T-04)
     */
    public function customerDetail($id)
    {
        $customer = Customer::findOrFail($id);
        $exposure = $customer->getCreditExposure();
        $allCustomers = Customer::all();
        $auditLogs = AuditLog::where('doc_id', $id)->orWhere('details', 'LIKE', "%{$id}%")->latest()->take(5)->get();

        return view('sales.customer_detail', compact('customer', 'exposure', 'allCustomers', 'auditLogs'));
    }

    /**
     * S-05 / F-06: Form Pembuatan Penawaran / Sales Order Baru
     */
    public function create(Request $request)
    {
        $customers = Customer::all();
        $warehouses = WarehouseLocation::all();
        $products = Product::all();
        $selectedCustomerId = $request->query('customer_id', 'C001');

        return view('sales.create', compact('customers', 'warehouses', 'products', 'selectedCustomerId'));
    }

    /**
     * Menyimpan Dokumen Sales Order Baru dengan Validasi Aturan Bisnis Ketat
     */
    public function store(Request $request)
    {
        $customerId = $request->input('customer_id');
        $warehouseCode = $request->input('warehouse_code', 'WH-JKT');
        $items = $request->input('items', []);

        if (empty($items)) {
            return back()->with('error', 'Pesanan harus memiliki minimal 1 produk!');
        }

        $customer = Customer::findOrFail($customerId);

        // 1. Validasi Aturan SR-07: Batas Diskon Maksimal 15% (Uji T-10)
        foreach ($items as $item) {
            $discount = (float) ($item['discount_percent'] ?? 0);
            if ($discount > 15) {
                AuditLog::record('Kevin Mahendra (Sales)', 'Sales', 'SO_REJECTED', 'SALES_ORDER', 'DRAFT', "Konfirmasi pesanan ditolak: Diskon {$discount}% melebihi batas otorisasi 15% (SR-07 / T-10).");
                return back()->withInput()->with('error', "Aturan SR-07 Dilanggar: Diskon baris maksimal yang diizinkan untuk Sales adalah 15%! Anda memasukkan {$discount}%. Sistem menolak pesanan ini.");
            }
        }

        // 2. Hitung Total DPP & PPN 11% (SR-16 / F-23)
        $amountUntaxed = 0;
        $amountTax = 0;
        $processedLines = [];

        foreach ($items as $item) {
            $sku = $item['variant_sku'];
            $qty = (int) $item['qty'];
            $price = (float) $item['unit_price'];
            $discount = (float) ($item['discount_percent'] ?? 0);

            // Subtotal setelah diskon dibulatkan ke rupiah
            $discountedPrice = $price * (1 - ($discount / 100));
            $lineSubtotal = round($discountedPrice * $qty);
            $lineTax = round($lineSubtotal * 0.11); // PPN 11% efektif

            $amountUntaxed += $lineSubtotal;
            $amountTax += $lineTax;

            $processedLines[] = [
                'variant_sku' => $sku,
                'qty' => $qty,
                'unit_price' => $price,
                'discount_percent' => $discount,
                'tax_rate' => 0.11,
                'subtotal' => $lineSubtotal,
            ];
        }

        $amountTotal = $amountUntaxed + $amountTax;

        // Generate nomor dokumen urut berikutnya (misal S00007)
        $lastOrder = SalesOrder::orderBy('id', 'desc')->first();
        $nextNum = $lastOrder ? ((int) preg_replace('/[^0-9]/', '', $lastOrder->id) + 1) : 7;
        $orderId = 'S' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        // Simpan dokumen sebagai Draft terlebih dahulu
        $order = DB::transaction(function () use ($orderId, $customerId, $warehouseCode, $amountUntaxed, $amountTax, $amountTotal, $processedLines) {
            $so = SalesOrder::create([
                'id' => $orderId,
                'customer_id' => $customerId,
                'warehouse_code' => $warehouseCode,
                'status' => 'Draft',
                'date' => now()->toDateString(),
                'amount_untaxed' => $amountUntaxed,
                'amount_tax' => $amountTax,
                'amount_total' => $amountTotal,
                'is_delivered' => false,
                'is_invoiced' => false,
            ]);

            foreach ($processedLines as $line) {
                $line['sales_order_id'] = $orderId;
                SalesOrderLine::create($line);
            }

            return $so;
        });

        AuditLog::record('Kevin Mahendra (Sales)', 'Sales', 'CREATE_SO', 'SALES_ORDER', $orderId, "Sales Order / Penawaran {$orderId} berhasil dibuat berstatus Draft.");

        // Jika tombol yang ditekan adalah "Save & Confirm"
        if ($request->input('action') === 'confirm') {
            return $this->confirm($order->id);
        }

        return redirect()->route('sales.show', $order->id)->with('success', "Penawaran {$order->id} berhasil dibuat!");
    }

    /**
     * S-05: Menampilkan Detail Sales Order
     */
    public function show($id)
    {
        $order = SalesOrder::with(['customer', 'lines.product', 'warehouse'])->findOrFail($id);
        $exposure = $order->customer->getCreditExposure();
        $auditLogs = AuditLog::where('doc_id', $id)->orWhere('details', 'LIKE', "%{$id}%")->latest()->get();

        return view('sales.show', compact('order', 'exposure', 'auditLogs'));
    }

    /**
     * Konfirmasi Sales Order (Eksekusi Aturan Bisnis Inti SR-04, SR-07, SR-08)
     */
    public function confirm($id)
    {
        $order = SalesOrder::with(['customer', 'lines'])->findOrFail($id);

        if ($order->status === 'Confirmed') {
            return back()->with('info', "Sales Order {$order->id} sudah berstatus Confirmed.");
        }

        // 1. Validasi Aturan SR-08: Pemeriksaan Batas Kredit (Uji T-04)
        $exposure = $order->customer->getCreditExposure();
        $projectedExposure = $exposure['total_exposure'] + (float) $order->amount_total;

        if ($projectedExposure > $exposure['credit_limit']) {
            AuditLog::record('Kevin Mahendra (Sales)', 'Sales', 'SO_CONFIRM_BLOCKED', 'SALES_ORDER', $order->id, 
                "Konfirmasi ditolak (SR-08 / T-04): Exposure Rp" . number_format($projectedExposure, 0, ',', '.') . " melampaui Credit Limit Rp" . number_format($exposure['credit_limit'], 0, ',', '.'));
            
            return back()->with('error', 
                "Konfirmasi Ditolak (Aturan SR-08 / Uji T-04)! Total Credit Exposure (Rp" . number_format($projectedExposure, 0, ',', '.') . ") " .
                "melampaui Credit Limit pelanggan (Rp" . number_format($exposure['credit_limit'], 0, ',', '.') . "). " .
                "Rincian: Unpaid AR Rp" . number_format($exposure['unpaid_ar'], 0, ',', '.') . ", Order Belum Ditagih Rp" . number_format($exposure['unbilled_so'], 0, ',', '.') . ", Pesanan Ini Rp" . number_format($order->amount_total, 0, ',', '.') . ".");
        }

        // 2. Validasi Aturan SR-04 & SR-03: Ketersediaan Stok & Reservasi (Uji T-11 & T-12)
        foreach ($order->lines as $line) {
            $stock = InventoryStock::where('location_code', $order->warehouse_code)
                ->where('variant_sku', $line->variant_sku)
                ->first();

            if (!$stock || $line->qty > $stock->available) {
                $available = $stock ? $stock->available : 0;
                AuditLog::record('Wahyu Saputra (Gudang)', 'Warehouse', 'RESERVATION_BLOCKED', 'SALES_ORDER', $order->id, 
                    "Reservasi ditolak (SR-04 / T-12): Permintaan {$line->qty} {$line->variant_sku} melebihi Available {$available} di {$order->warehouse_code}.");

                return back()->with('error', 
                    "Peringatan Ketersediaan Stok (Aturan SR-04 / Uji T-12): Stok Available untuk {$line->variant_sku} di {$order->warehouse_code} hanya tersisa {$available} unit. " .
                    "Sistem melarang reservasi yang membuat Available negatif!");
            }
        }

        // 3. Eksekusi Reservasi Stok & Ubah Status ke Confirmed (F-06 / F-08)
        DB::transaction(function () use ($order) {
            foreach ($order->lines as $line) {
                $stock = InventoryStock::where('location_code', $order->warehouse_code)
                    ->where('variant_sku', $line->variant_sku)
                    ->first();

                // Reserved bertambah, Available berkurang, On Hand (Fisik) tetap
                $stock->reserved += $line->qty;
                $stock->available = $stock->on_hand - $stock->reserved;
                $stock->save();
            }

            $order->status = 'Confirmed';
            $order->save();

            // Otomatis terbitkan Delivery Order untuk Gudang (F-09)
            $doId = 'WH/OUT/' . substr($order->id, 1);
            DB::table('delivery_orders')->insert([
                'id' => $doId,
                'sales_order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'warehouse_code' => $order->warehouse_code,
                'status' => 'Confirmed',
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($order->lines as $line) {
                DB::table('delivery_order_lines')->insert([
                    'delivery_order_id' => $doId,
                    'variant_sku' => $line->variant_sku,
                    'demand' => $line->qty,
                    'reserved' => $line->qty,
                    'done' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        AuditLog::record('Kevin Mahendra (Sales)', 'Sales', 'SO_CONFIRMED', 'SALES_ORDER', $order->id, 
            "Sales Order {$order->id} berhasil dikonfirmasi. Harga dan diskon terkunci (Price Lock). Stok di-reserve di {$order->warehouse_code}. Delivery Order dibuat otomatis.");

        return redirect()->route('sales.show', $order->id)->with('success', 
            "Sukses! Sales Order {$order->id} berhasil dikonfirmasi. Stok berhasil di-reserve di gudang dan Delivery Order otomatis terbit!");
    }

    /**
     * Tombol Wajib: Reset Database Kembali ke Baseline Seed Resmi
     */
    public function resetSeed()
    {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        
        return redirect()->route('sales.index')->with('success', 
            'DATABASE BERHASIL DIRESET! Seluruh data telah kembali ke posisi Baseline Seed resmi (Kas Bank Rp50 Juta, 6 Pelanggan, 15 Produk, Stok 396 unit).');
    }
}

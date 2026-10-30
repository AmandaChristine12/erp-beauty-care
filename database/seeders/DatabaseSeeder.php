<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Memasukkan Dataset Baseline Resmi PT Arunika Beauty Nusantara (NusaGlow)
     * Sesuai tabel di Student Scenario Handout (Halaman 6 - 9).
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara untuk truncate bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('audit_logs')->truncate();
        DB::table('employees')->truncate();
        DB::table('journal_entries')->truncate();
        DB::table('chart_of_accounts')->truncate();
        DB::table('customer_invoices')->truncate();
        DB::table('delivery_order_lines')->truncate();
        DB::table('delivery_orders')->truncate();
        DB::table('sales_order_lines')->truncate();
        DB::table('sales_orders')->truncate();
        DB::table('inventory_stocks')->truncate();
        DB::table('warehouses_locations')->truncate();
        DB::table('products')->truncate();
        DB::table('contacts_vendors')->truncate();
        DB::table('contacts_customers')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. contacts_customers (Halaman 6)
        DB::table('contacts_customers')->insert([
            ['id' => 'C001', 'name' => 'Ayu Prameswari', 'type' => 'Perorangan', 'city' => 'Jakarta', 'credit_limit' => 2000000, 'payment_terms' => 'Net 14', 'opening_receivable' => 0],
            ['id' => 'C002', 'name' => 'Nadira Putri', 'type' => 'Perorangan', 'city' => 'Bandung', 'credit_limit' => 500000, 'payment_terms' => 'Net 14', 'opening_receivable' => 222000],
            ['id' => 'C003', 'name' => 'PT Anggun Sejahtera', 'type' => 'Perusahaan', 'city' => 'Jakarta', 'credit_limit' => 8000000, 'payment_terms' => 'Net 30', 'opening_receivable' => 4218000],
            ['id' => 'C004', 'name' => 'Rani Kartika', 'type' => 'Perorangan', 'city' => 'Depok', 'credit_limit' => 1200000, 'payment_terms' => 'Immediate', 'opening_receivable' => 0],
            ['id' => 'C005', 'name' => 'Dewi Natalia', 'type' => 'Perorangan', 'city' => 'Bekasi', 'credit_limit' => 1000000, 'payment_terms' => 'Net 14', 'opening_receivable' => 0],
            ['id' => 'C006', 'name' => 'CV Cantik Bersama', 'type' => 'Perusahaan', 'city' => 'Bandung', 'credit_limit' => 4000000, 'payment_terms' => 'Net 30', 'opening_receivable' => 0],
        ]);

        // 2. contacts_vendors (Halaman 6)
        DB::table('contacts_vendors')->insert([
            ['id' => 'V01', 'name' => 'PT Bumi Dermalab', 'city' => 'Jakarta', 'payment_terms' => 'Net 30'],
            ['id' => 'V02', 'name' => 'CV Aura Kosmetika', 'city' => 'Bandung', 'payment_terms' => 'Net 14'],
            ['id' => 'V03', 'name' => 'PT Proteksi Kulit', 'city' => 'Bogor', 'payment_terms' => 'Net 30'],
            ['id' => 'V04', 'name' => 'CV Natura Indah', 'city' => 'Jakarta', 'payment_terms' => 'Net 14'],
        ]);

        // 3. products (Halaman 6 - 7)
        DB::table('products')->insert([
            ['variant_sku' => 'BC-CLN-100', 'name' => 'Gentle Cleanser', 'size' => '100 ml', 'color' => 'Bening', 'unit' => 'botol', 'sales_price' => 120000, 'cost' => 60000, 'default_vendor' => 'V01'],
            ['variant_sku' => 'BC-TON-100', 'name' => 'Hydrating Toner', 'size' => '100 ml', 'color' => 'Bening', 'unit' => 'botol', 'sales_price' => 150000, 'cost' => 70000, 'default_vendor' => 'V01'],
            ['variant_sku' => 'BC-SER-030', 'name' => 'Glow Serum', 'size' => '30 ml', 'color' => 'Amber', 'unit' => 'botol', 'sales_price' => 90000, 'cost' => 45000, 'default_vendor' => 'V02'],
            ['variant_sku' => 'BC-MSK-001', 'name' => 'Facial Treatment Mask', 'size' => '1 sachet', 'color' => 'Putih', 'unit' => 'sachet', 'sales_price' => 85000, 'cost' => 40000, 'default_vendor' => 'V02'],
            ['variant_sku' => 'BC-SCR-050', 'name' => 'Face Scrub', 'size' => '50 ml', 'color' => 'Putih', 'unit' => 'jar', 'sales_price' => 110000, 'cost' => 55000, 'default_vendor' => 'V01'],
            ['variant_sku' => 'BC-SPF-050', 'name' => 'Daily Sunscreen SPF50', 'size' => '50 ml', 'color' => 'Krem', 'unit' => 'tube', 'sales_price' => 130000, 'cost' => 65000, 'default_vendor' => 'V03'],
            ['variant_sku' => 'BC-CRM-050', 'name' => 'Day Cream', 'size' => '50 g', 'color' => 'Putih', 'unit' => 'jar', 'sales_price' => 165000, 'cost' => 82500, 'default_vendor' => 'V03'],
            ['variant_sku' => 'BC-CRM-051', 'name' => 'Night Cream', 'size' => '50 g', 'color' => 'Ungu', 'unit' => 'jar', 'sales_price' => 175000, 'cost' => 87500, 'default_vendor' => 'V03'],
            ['variant_sku' => 'BC-LIP-001', 'name' => 'Lip Balm', 'size' => '10 g', 'color' => 'Rose', 'unit' => 'tube', 'sales_price' => 65000, 'cost' => 30000, 'default_vendor' => 'V04'],
            ['variant_sku' => 'BC-LIP-002', 'name' => 'Lip Balm', 'size' => '10 g', 'color' => 'Coral', 'unit' => 'tube', 'sales_price' => 65000, 'cost' => 30000, 'default_vendor' => 'V04'],
            ['variant_sku' => 'BC-HAI-100', 'name' => 'Hair Serum', 'size' => '100 ml', 'color' => 'Amber', 'unit' => 'botol', 'sales_price' => 145000, 'cost' => 72000, 'default_vendor' => 'V02'],
            ['variant_sku' => 'BC-SHA-250', 'name' => 'Gentle Shampoo', 'size' => '250 ml', 'color' => 'Bening', 'unit' => 'botol', 'sales_price' => 95000, 'cost' => 47000, 'default_vendor' => 'V04'],
            ['variant_sku' => 'BC-CND-250', 'name' => 'Hair Conditioner', 'size' => '250 ml', 'color' => 'Putih', 'unit' => 'botol', 'sales_price' => 105000, 'cost' => 52000, 'default_vendor' => 'V04'],
            ['variant_sku' => 'BC-BDY-200', 'name' => 'Body Lotion', 'size' => '200 ml', 'color' => 'Putih', 'unit' => 'botol', 'sales_price' => 115000, 'cost' => 57000, 'default_vendor' => 'V01'],
            ['variant_sku' => 'BC-MST-100', 'name' => 'Body Mist', 'size' => '100 ml', 'color' => 'Pink', 'unit' => 'botol', 'sales_price' => 125000, 'cost' => 62000, 'default_vendor' => 'V04'],
        ]);

        // 4. warehouses_locations (Halaman 7)
        DB::table('warehouses_locations')->insert([
            ['location_code' => 'WH-JKT', 'location_name' => 'Gudang pusat', 'city' => 'Jakarta', 'type' => 'Warehouse'],
            ['location_code' => 'ST-BDG', 'location_name' => 'Glow Studio / retail', 'city' => 'Bandung', 'type' => 'Store / studio'],
        ]);

        // 5. inventory_stocks (Halaman 7 - 8)
        // Control baseline: 396 unit; total valuasi Rp22.612.500
        // Dokumen S00006 membuka reservasi 3 unit BC-CLN-100 di WH-JKT (On hand 15, Reserved 3, Available 12)
        $stocks = [
            ['sku' => 'BC-CLN-100', 'wh' => 15, 'st' => 8, 'wh_res' => 3],
            ['sku' => 'BC-TON-100', 'wh' => 17, 'st' => 9, 'wh_res' => 0],
            ['sku' => 'BC-SER-030', 'wh' => 19, 'st' => 10, 'wh_res' => 0],
            ['sku' => 'BC-MSK-001', 'wh' => 15, 'st' => 11, 'wh_res' => 0],
            ['sku' => 'BC-SCR-050', 'wh' => 17, 'st' => 8, 'wh_res' => 0],
            ['sku' => 'BC-SPF-050', 'wh' => 19, 'st' => 9, 'wh_res' => 0],
            ['sku' => 'BC-CRM-050', 'wh' => 15, 'st' => 10, 'wh_res' => 0],
            ['sku' => 'BC-CRM-051', 'wh' => 17, 'st' => 11, 'wh_res' => 0],
            ['sku' => 'BC-LIP-001', 'wh' => 19, 'st' => 8, 'wh_res' => 0],
            ['sku' => 'BC-LIP-002', 'wh' => 15, 'st' => 9, 'wh_res' => 0],
            ['sku' => 'BC-HAI-100', 'wh' => 17, 'st' => 10, 'wh_res' => 0],
            ['sku' => 'BC-SHA-250', 'wh' => 19, 'st' => 11, 'wh_res' => 0],
            ['sku' => 'BC-CND-250', 'wh' => 15, 'st' => 8, 'wh_res' => 0],
            ['sku' => 'BC-BDY-200', 'wh' => 17, 'st' => 9, 'wh_res' => 0],
            ['sku' => 'BC-MST-100', 'wh' => 19, 'st' => 10, 'wh_res' => 0],
        ];

        foreach ($stocks as $s) {
            // Gudang WH-JKT
            DB::table('inventory_stocks')->insert([
                'location_code' => 'WH-JKT',
                'variant_sku' => $s['sku'],
                'on_hand' => $s['wh'],
                'reserved' => $s['wh_res'],
                'available' => $s['wh'] - $s['wh_res'],
            ]);
            // Toko/Studio ST-BDG
            DB::table('inventory_stocks')->insert([
                'location_code' => 'ST-BDG',
                'variant_sku' => $s['sku'],
                'on_hand' => $s['st'],
                'reserved' => 0,
                'available' => $s['st'],
            ]);
        }

        // 6. chart_of_accounts (Halaman 8 - 9)
        DB::table('chart_of_accounts')->insert([
            ['code' => '1010', 'account_name' => 'Bank', 'type' => 'Asset', 'balance' => 50000000],
            ['code' => '1100', 'account_name' => 'Accounts Receivable', 'type' => 'Asset', 'balance' => 4440000],
            ['code' => '1150', 'account_name' => 'Input VAT', 'type' => 'Asset', 'balance' => 0],
            ['code' => '1200', 'account_name' => 'Stock Valuation', 'type' => 'Asset', 'balance' => 22612500],
            ['code' => '1250', 'account_name' => 'Stock Interim Received', 'type' => 'Asset', 'balance' => 0],
            ['code' => '2100', 'account_name' => 'Accounts Payable', 'type' => 'Liability', 'balance' => 2220000],
            ['code' => '2150', 'account_name' => 'Output VAT', 'type' => 'Liability', 'balance' => 0],
            ['code' => '3100', 'account_name' => 'Opening Capital', 'type' => 'Equity', 'balance' => 74832500],
            ['code' => '4000', 'account_name' => 'Sales Revenue', 'type' => 'Income', 'balance' => 0],
            ['code' => '4010', 'account_name' => 'Service Revenue', 'type' => 'Income', 'balance' => 0],
            ['code' => '5000', 'account_name' => 'Cost of Goods Sold', 'type' => 'Expense', 'balance' => 0],
            ['code' => '5010', 'account_name' => 'Treatment Consumables', 'type' => 'Expense', 'balance' => 0],
            ['code' => '6000', 'account_name' => 'Operating Expense', 'type' => 'Expense', 'balance' => 0],
            ['code' => '6100', 'account_name' => 'Bank Fees', 'type' => 'Expense', 'balance' => 0],
            ['code' => '9999', 'account_name' => 'Opening Balance Clearing', 'type' => 'Equity', 'balance' => 0],
        ]);

        // 7. employees (Halaman 9)
        DB::table('employees')->insert([
            ['employee_id' => 'E001', 'name' => 'Sinta Maharani', 'job_position' => 'Beauty Therapist', 'department' => 'Studio Bandung', 'skill' => 'Facial', 'status' => 'Aktif'],
            ['employee_id' => 'E002', 'name' => 'Rizka Amalia', 'job_position' => 'Beauty Therapist', 'department' => 'Studio Bandung', 'skill' => 'Hair Treatment', 'status' => 'Aktif'],
            ['employee_id' => 'E003', 'name' => 'Gita Lestari', 'job_position' => 'Supervisor', 'department' => 'Studio Bandung', 'skill' => 'Facial; Hair Treatment', 'status' => 'Aktif'],
            ['employee_id' => 'E004', 'name' => 'Fajar Wijaya', 'job_position' => 'Stock Operator', 'department' => 'Gudang Jakarta', 'skill' => 'Stock Handling', 'status' => 'Aktif'],
        ]);

        // 8. Dokumen aktif awal S00006 (Halaman 8)
        DB::table('sales_orders')->insert([
            'id' => 'S00006',
            'customer_id' => 'C005',
            'warehouse_code' => 'WH-JKT',
            'status' => 'Confirmed',
            'date' => '2026-10-20',
            'amount_untaxed' => 360000,
            'amount_tax' => 39600,
            'amount_total' => 399600,
            'is_delivered' => false,
            'is_invoiced' => false,
        ]);

        DB::table('sales_order_lines')->insert([
            'sales_order_id' => 'S00006',
            'variant_sku' => 'BC-CLN-100',
            'qty' => 3,
            'unit_price' => 120000,
            'discount_percent' => 0,
            'tax_rate' => 0.11,
            'subtotal' => 360000,
        ]);

        // 9. Initial Audit Log
        DB::table('audit_logs')->insert([
            'timestamp' => now(),
            'actor' => 'System Baseline',
            'role' => 'System',
            'action' => 'RESET_BASELINE_SEED',
            'doc_type' => 'DATABASE',
            'doc_id' => 'ALL',
            'details' => 'Database berhasil dimigrasikan dan diisi data seed baseline resmi PT Arunika Beauty Nusantara.'
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skema Basis Data Resmi ERP PT Arunika Beauty Nusantara (NusaGlow)
     * Mengikuti spesifikasi Student Scenario Handout (Halaman 5 - 9).
     */
    public function up(): void
    {
        // 1. Master Pelanggan (contacts_customers)
        Schema::create('contacts_customers', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // C001, C002, dst.
            $table->string('name');
            $table->string('type'); // Perorangan, Perusahaan
            $table->string('city');
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->string('payment_terms')->default('Net 14');
            $table->decimal('opening_receivable', 15, 2)->default(0);
            $table->timestamps();
        });

        // 2. Master Pemasok (contacts_vendors)
        Schema::create('contacts_vendors', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // V01, V02, dst.
            $table->string('name');
            $table->string('city');
            $table->string('payment_terms')->default('Net 30');
            $table->timestamps();
        });

        // 3. Master Produk & Varian SKU (products)
        Schema::create('products', function (Blueprint $table) {
            $table->string('variant_sku', 30)->primary(); // BC-CLN-100, dst.
            $table->string('name');
            $table->string('size', 50);
            $table->string('color', 50);
            $table->string('unit', 30);
            $table->decimal('sales_price', 15, 2)->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('default_vendor', 20)->nullable();
            $table->timestamps();
        });

        // 4. Lokasi Gudang & Toko (warehouses_locations)
        Schema::create('warehouses_locations', function (Blueprint $table) {
            $table->string('location_code', 20)->primary(); // WH-JKT, ST-BDG
            $table->string('location_name');
            $table->string('city');
            $table->string('type'); // Warehouse, Store / studio
            $table->timestamps();
        });

        // 5. Saldo Stok per Lokasi (inventory_stocks)
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('location_code', 20);
            $table->string('variant_sku', 30);
            $table->integer('on_hand')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('available')->default(0);
            $table->timestamps();

            $table->unique(['location_code', 'variant_sku']);
            $table->foreign('location_code')->references('location_code')->on('warehouses_locations')->cascadeOnDelete();
            $table->foreign('variant_sku')->references('variant_sku')->on('products')->cascadeOnDelete();
        });

        // 6. Dokumen Sales Order (sales_orders) - Modul Anggota 1
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->string('id', 30)->primary(); // S00006, S00007, Q00007
            $table->string('customer_id', 20);
            $table->string('warehouse_code', 20)->default('WH-JKT');
            $table->string('status', 30)->default('Draft'); // Draft, Confirmed, Done, Cancelled
            $table->date('date');
            $table->decimal('amount_untaxed', 15, 2)->default(0);
            $table->decimal('amount_tax', 15, 2)->default(0); // PPN 11%
            $table->decimal('amount_total', 15, 2)->default(0);
            $table->boolean('is_delivered')->default(false);
            $table->boolean('is_invoiced')->default(false);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('contacts_customers')->cascadeOnDelete();
            $table->foreign('warehouse_code')->references('location_code')->on('warehouses_locations')->cascadeOnDelete();
        });

        // 7. Rincian Baris Sales Order (sales_order_lines)
        Schema::create('sales_order_lines', function (Blueprint $table) {
            $table->id();
            $table->string('sales_order_id', 30);
            $table->string('variant_sku', 30);
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0); // Max 15% (SR-07)
            $table->decimal('tax_rate', 5, 2)->default(0.11); // 11% PPN (SR-16)
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->cascadeOnDelete();
            $table->foreign('variant_sku')->references('variant_sku')->on('products')->cascadeOnDelete();
        });

        // 8. Dokumen Delivery Order (delivery_orders)
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->string('id', 30)->primary(); // WH/OUT/00007
            $table->string('sales_order_id', 30);
            $table->string('customer_id', 20);
            $table->string('warehouse_code', 20);
            $table->string('status', 30)->default('Draft'); // Draft, Ready, Done
            $table->date('date');
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('contacts_customers')->cascadeOnDelete();
        });

        // 9. Rincian Baris Pengiriman (delivery_order_lines)
        Schema::create('delivery_order_lines', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_order_id', 30);
            $table->string('variant_sku', 30);
            $table->integer('demand')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('done')->default(0);
            $table->timestamps();

            $table->foreign('delivery_order_id')->references('id')->on('delivery_orders')->cascadeOnDelete();
            $table->foreign('variant_sku')->references('variant_sku')->on('products')->cascadeOnDelete();
        });

        // 10. Faktur Tagihan Pelanggan (customer_invoices)
        Schema::create('customer_invoices', function (Blueprint $table) {
            $table->string('id', 30)->primary(); // INV/2026/00007
            $table->string('customer_id', 20);
            $table->string('source_doc', 100); // S00007 / WH/OUT/00007
            $table->decimal('amount_untaxed', 15, 2)->default(0);
            $table->decimal('amount_tax', 15, 2)->default(0);
            $table->decimal('amount_total', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->string('status', 30)->default('Draft'); // Draft, Posted, Paid
            $table->date('date');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('contacts_customers')->cascadeOnDelete();
        });

        // 11. Bagan Akun Akuntansi (chart_of_accounts)
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->string('code', 20)->primary(); // 1010, 1100, dst.
            $table->string('account_name');
            $table->string('type'); // Asset, Liability, Equity, Income, Expense
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });

        // 12. Jurnal Umum (journal_entries)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // JE/2026/00007
            $table->date('date');
            $table->string('reference', 100)->nullable();
            $table->string('debit_account', 100);
            $table->string('credit_account', 100);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status', 30)->default('Posted');
            $table->timestamps();
        });

        // 13. Data Karyawan & Skill (employees)
        Schema::create('employees', function (Blueprint $table) {
            $table->string('employee_id', 20)->primary(); // E001, E002, dst.
            $table->string('name');
            $table->string('job_position');
            $table->string('department');
            $table->string('skill'); // Facial, Hair Treatment, dll.
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });

        // 14. Audit Trail Log (audit_logs) - SR-12
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp')->useCurrent();
            $table->string('actor');
            $table->string('role');
            $table->string('action');
            $table->string('doc_type');
            $table->string('doc_id');
            $table->text('details');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
        Schema::dropIfExists('customer_invoices');
        Schema::dropIfExists('delivery_order_lines');
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('sales_order_lines');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('warehouses_locations');
        Schema::dropIfExists('products');
        Schema::dropIfExists('contacts_vendors');
        Schema::dropIfExists('contacts_customers');
    }
};

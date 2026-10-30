@extends('layouts.odoo')

@section('title', 'Buat Penawaran / Sales Order Baru - NusaGlow ERP')

@section('breadcrumbs')
    <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-gray-700">Orders</a>
    <span class="text-gray-400">/</span>
    <span class="font-bold text-gray-800">New Quotation (S-05)</span>
@endsection

@section('control-actions')
    <a href="{{ route('sales.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-1.5 rounded text-xs">
        Batal
    </a>
@endsection

@section('content')
<form action="{{ route('sales.store') }}" method="POST" id="so-form">
    @csrf

    <div class="bg-white rounded border border-gray-200 shadow-sm p-6 mb-6">
        <!-- Statusbar Pipeline -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
            <div class="flex gap-2">
                <button type="submit" name="action" value="save" class="bg-odoo hover-bg-odoo text-white font-semibold px-4 py-1.5 rounded shadow text-xs">
                    Simpan Draft (Quotation)
                </button>
                <button type="submit" name="action" value="confirm" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-1.5 rounded shadow text-xs">
                    Simpan & Langsung Confirm (SO)
                </button>
            </div>
            <div class="flex items-center text-xs font-semibold">
                <span class="bg-odoo text-white px-3 py-1 rounded-l">Quotation / Draft</span>
                <span class="bg-gray-200 text-gray-500 px-3 py-1 rounded-r">Sales Order</span>
            </div>
        </div>

        <h1 class="text-xl font-bold text-odoo mb-4">Penawaran Baru (Quotation)</h1>

        <!-- Form Grid Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Customer (Pelanggan)</label>
                <select name="customer_id" id="customer_select" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:border-purple-600" onchange="updateCustomerInfo()">
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-terms="{{ $c->payment_terms }}" data-limit="{{ $c->credit_limit }}" data-ar="{{ $c->opening_receivable }}" {{ $c->id === $selectedCustomerId ? 'selected' : '' }}>
                            {{ $c->id }} - {{ $c->name }} (Limit: Rp{{ number_format($c->credit_limit, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                <div id="customer-exposure-badge" class="mt-2 text-xs p-2 rounded bg-gray-50 border border-gray-200">
                    <!-- Updated via JS -->
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Gudang Pemenuhan (Warehouse)</label>
                    <select name="warehouse_code" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:outline-none focus:border-purple-600">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->location_code }}">{{ $w->location_code }} - {{ $w->location_name }} ({{ $w->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Payment Terms</label>
                    <input type="text" id="display_terms" class="w-full border border-gray-200 bg-gray-50 rounded px-3 py-2 text-xs text-gray-600 font-medium" readonly value="Net 14" />
                </div>
            </div>
        </div>

        <!-- Order Lines Table -->
        <div class="mt-6">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold text-gray-800 text-sm">Order Lines (Rincian Produk)</h3>
                <button type="button" onclick="addRow()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-2.5 py-1 rounded text-xs border border-gray-300">
                    + Tambah Baris Produk
                </button>
            </div>

            <table class="w-full text-left text-xs border border-gray-200" id="lines-table">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="py-2.5 px-3">Produk Variant (SKU)</th>
                        <th class="py-2.5 px-3 w-24">Qty</th>
                        <th class="py-2.5 px-3 w-32">Harga Satuan (Rp)</th>
                        <th class="py-2.5 px-3 w-32">Diskon (%) <span class="text-red-500 font-bold">*Maks 15%</span></th>
                        <th class="py-2.5 px-3 w-24">Pajak</th>
                        <th class="py-2.5 px-3 text-right w-36">Subtotal (Rp)</th>
                        <th class="py-2.5 px-3 w-12 text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="lines-body">
                    <tr class="line-row">
                        <td class="py-2 px-3">
                            <select name="items[0][variant_sku]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs sku-select" onchange="onSkuChange(this)">
                                @foreach($products as $p)
                                    <option value="{{ $p->variant_sku }}" data-price="{{ $p->sales_price }}" {{ $p->variant_sku === 'BC-CLN-100' ? 'selected' : '' }}>
                                        [{{ $p->variant_sku }}] {{ $p->name }} ({{ $p->size }}) - Rp{{ number_format($p->sales_price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="py-2 px-3">
                            <input type="number" name="items[0][qty]" value="2" min="1" class="w-full border border-gray-300 rounded px-2 py-1 text-xs qty-input" oninput="calculateTotals()" />
                        </td>
                        <td class="py-2 px-3">
                            <input type="number" name="items[0][unit_price]" value="120000" class="w-full border border-gray-300 rounded px-2 py-1 text-xs price-input" oninput="calculateTotals()" />
                        </td>
                        <td class="py-2 px-3">
                            <input type="number" name="items[0][discount_percent]" value="0" min="0" max="100" class="w-full border border-gray-300 rounded px-2 py-1 text-xs discount-input" oninput="checkDiscountRule(this); calculateTotals();" />
                        </td>
                        <td class="py-2 px-3">
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[11px] font-mono">PPN 11%</span>
                        </td>
                        <td class="py-2 px-3 text-right font-bold text-gray-800 line-subtotal">
                            Rp240.000
                        </td>
                        <td class="py-2 px-3 text-center">
                            <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 font-bold">✕</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Summary Totals Box -->
            <div class="mt-4 flex justify-end">
                <div class="w-72 bg-gray-50 border border-gray-200 rounded p-4 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-600">
                        <span>DPP (Untaxed):</span>
                        <strong id="display-untaxed">Rp240.000</strong>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>PPN 11% (Efektif SR-16):</span>
                        <strong id="display-vat">Rp26.400</strong>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-odoo border-t border-gray-300 pt-2">
                        <span>Total Tagihan:</span>
                        <strong id="display-grand">Rp266.400</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    let rowIndex = 1;

    function onSkuChange(select) {
        const selected = select.options[select.selectedIndex];
        const price = selected.getAttribute('data-price') || 0;
        const row = select.closest('tr');
        row.querySelector('.price-input').value = price;
        calculateTotals();
    }

    function checkDiscountRule(input) {
        const val = parseFloat(input.value) || 0;
        if (val > 15) {
            input.classList.add('border-red-500', 'bg-red-50');
            alert('PERHATIAN ATURAN SR-07: Diskon baris maksimal untuk Sales adalah 15%! Lebih dari itu akan ditolak oleh sistem.');
        } else {
            input.classList.remove('border-red-500', 'bg-red-50');
        }
    }

    function calculateTotals() {
        const rows = document.querySelectorAll('.line-row');
        let totalUntaxed = 0;
        let totalVat = 0;

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

            const discountedPrice = price * (1 - (discount / 100));
            const subtotal = Math.round(discountedPrice * qty);
            const vat = Math.round(subtotal * 0.11);

            row.querySelector('.line-subtotal').innerText = 'Rp' + subtotal.toLocaleString('id-ID');

            totalUntaxed += subtotal;
            totalVat += vat;
        });

        const grand = totalUntaxed + totalVat;
        document.getElementById('display-untaxed').innerText = 'Rp' + totalUntaxed.toLocaleString('id-ID');
        document.getElementById('display-vat').innerText = 'Rp' + totalVat.toLocaleString('id-ID');
        document.getElementById('display-grand').innerText = 'Rp' + grand.toLocaleString('id-ID');
    }

    function addRow() {
        const tbody = document.getElementById('lines-body');
        const firstRow = document.querySelector('.line-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('.sku-select').name = `items[${rowIndex}][variant_sku]`;
        newRow.querySelector('.qty-input').name = `items[${rowIndex}][qty]`;
        newRow.querySelector('.qty-input').value = 1;
        newRow.querySelector('.price-input').name = `items[${rowIndex}][unit_price]`;
        newRow.querySelector('.discount-input').name = `items[${rowIndex}][discount_percent]`;
        newRow.querySelector('.discount-input').value = 0;

        tbody.appendChild(newRow);
        rowIndex++;
        calculateTotals();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.line-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calculateTotals();
        } else {
            alert('Minimal harus ada 1 baris produk!');
        }
    }

    function updateCustomerInfo() {
        const select = document.getElementById('customer_select');
        const opt = select.options[select.selectedIndex];
        document.getElementById('display_terms').value = opt.getAttribute('data-terms');
        
        const limit = parseFloat(opt.getAttribute('data-limit')) || 0;
        const ar = parseFloat(opt.getAttribute('data-ar')) || 0;
        
        document.getElementById('customer-exposure-badge').innerHTML = `
            <strong>Plafon Kredit:</strong> Rp${limit.toLocaleString('id-ID')} | 
            <strong>Piutang Lama (Unpaid AR):</strong> <span class="text-red-600 font-semibold">Rp${ar.toLocaleString('id-ID')}</span>
        `;
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCustomerInfo();
        calculateTotals();
    });
</script>
@endsection

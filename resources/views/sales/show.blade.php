@extends('layouts.odoo')

@section('title', 'Sales Order ' . $order->id . ' - NusaGlow ERP')

@section('breadcrumbs')
    <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-gray-700">Orders</a>
    <span class="text-gray-400">/</span>
    <span class="font-bold text-gray-800">{{ $order->id }} (S-05)</span>
@endsection

@section('control-actions')
    <a href="{{ route('sales.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-1.5 rounded text-xs">
        Daftar SO
    </a>
    <a href="{{ route('sales.create') }}" class="bg-odoo hover-bg-odoo text-white font-semibold px-3 py-1.5 rounded shadow text-xs">
        + Buat Baru
    </a>
@endsection

@section('content')
<div class="bg-white rounded border border-gray-200 shadow-sm p-6 mb-6">
    <!-- Statusbar Actions & Pipeline -->
    <div class="flex flex-wrap justify-between items-center border-b border-gray-200 pb-4 mb-6 gap-3">
        <div class="flex items-center gap-2">
            @if($order->status === 'Draft')
                <form action="{{ route('sales.confirm', $order->id) }}" method="POST" onsubmit="return confirm('Konfirmasi Sales Order ini dan lakukan reservasi stok di gudang?');">
                    @csrf
                    <button type="submit" class="bg-odoo hover-bg-odoo text-white font-bold px-4 py-1.5 rounded shadow text-xs flex items-center gap-1.5">
                        <span>✓</span> Confirm (Konfirmasi SO)
                    </button>
                </form>
            @else
                <span class="bg-green-600 text-white font-bold px-3 py-1.5 rounded text-xs flex items-center gap-1">
                    <span>✓</span> Terkonfirmasi (Confirmed)
                </span>
                <span class="text-xs text-gray-500 italic bg-gray-100 px-2 py-1 rounded">
                    🔒 Harga & Diskon Terkunci (Price Lock SR-07)
                </span>
            @endif
        </div>

        <div class="flex items-center text-xs font-semibold">
            <span class="{{ $order->status === 'Draft' ? 'bg-odoo text-white font-bold' : 'bg-gray-200 text-gray-600' }} px-3 py-1 rounded-l">
                Draft / Quotation
            </span>
            <span class="{{ $order->status === 'Confirmed' ? 'bg-green-600 text-white font-bold' : 'bg-gray-200 text-gray-400' }} px-3 py-1 rounded-r">
                Sales Order (Confirmed)
            </span>
        </div>
    </div>

    <!-- Smart Buttons Box -->
    <div class="flex justify-end gap-3 mb-6">
        <a href="{{ route('sales.customer.detail', $order->customer_id) }}" class="bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded px-3 py-1.5 flex items-center gap-2 text-xs">
            <span class="text-base">👤</span>
            <div>
                <div class="font-bold text-gray-800">{{ $order->customer_id }}</div>
                <div class="text-[10px] text-gray-500 uppercase">Cek Plafon Kredit</div>
            </div>
        </a>
        @if($order->status === 'Confirmed')
            <div class="bg-green-50 border border-green-200 rounded px-3 py-1.5 flex items-center gap-2 text-xs">
                <span class="text-base">🚚</span>
                <div>
                    <div class="font-bold text-green-900">WH/OUT/{{ substr($order->id, 1) }}</div>
                    <div class="text-[10px] text-green-700 uppercase">Delivery Order Siap</div>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-odoo">{{ $order->status === 'Confirmed' ? 'Sales Order' : 'Quotation' }} #{{ $order->id }}</h1>
        <p class="text-xs text-gray-500">PT Arunika Beauty Nusantara • Brand NusaGlow</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">Customer (Pelanggan)</label>
            <div class="text-sm font-semibold text-gray-800">{{ $order->customer->name }} ({{ $order->customer_id }})</div>
            <div class="text-xs text-gray-500">{{ $order->customer->type }} • {{ $order->customer->city }}</div>
            
            <div class="mt-3 p-2.5 rounded bg-purple-50 border border-purple-100 text-xs">
                <div class="font-semibold text-purple-900">Credit Exposure Pelanggan (SR-08):</div>
                <div class="text-gray-700 mt-0.5">
                    Limit: Rp{{ number_format($exposure['credit_limit'], 0, ',', '.') }} | 
                    Total Exposure: <strong class="{{ $exposure['is_exceeded'] ? 'text-red-600' : 'text-green-700' }}">Rp{{ number_format($exposure['total_exposure'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Gudang Pemenuhan</label>
                <div class="text-xs font-semibold text-gray-800">{{ $order->warehouse_code }} - {{ $order->warehouse->location_name ?? 'Gudang Pusat' }}</div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Dokumen</label>
                <div class="text-xs text-gray-700">{{ $order->date }}</div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Syarat Pembayaran (Payment Terms)</label>
                <div class="text-xs text-gray-700">{{ $order->customer->payment_terms }}</div>
            </div>
        </div>
    </div>

    <!-- Lines Table -->
    <div class="mt-6">
        <h3 class="font-bold text-gray-800 text-sm mb-2">Order Lines</h3>
        <table class="w-full text-left text-xs border border-gray-200">
            <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                <tr>
                    <th class="py-2.5 px-3">Produk Variant (SKU)</th>
                    <th class="py-2.5 px-3 text-right">Qty</th>
                    <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                    <th class="py-2.5 px-3 text-right">Diskon (%)</th>
                    <th class="py-2.5 px-3 text-center">Pajak</th>
                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($order->lines as $line)
                    <tr>
                        <td class="py-2.5 px-3">
                            <strong>[{{ $line->variant_sku }}]</strong> {{ $line->product->name ?? '' }} ({{ $line->product->size ?? '' }})
                        </td>
                        <td class="py-2.5 px-3 text-right font-semibold">{{ $line->qty }}</td>
                        <td class="py-2.5 px-3 text-right">Rp{{ number_format($line->unit_price, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-right text-gray-600">{{ $line->discount_percent }}%</td>
                        <td class="py-2.5 px-3 text-center">
                            <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[11px] font-mono">PPN 11%</span>
                        </td>
                        <td class="py-2.5 px-3 text-right font-bold text-gray-900">
                            Rp{{ number_format($line->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 flex justify-end">
            <div class="w-72 bg-gray-50 border border-gray-200 rounded p-4 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>DPP (Untaxed Amount):</span>
                    <strong>Rp{{ number_format($order->amount_untaxed, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>PPN 11% (SR-16):</span>
                    <strong>Rp{{ number_format($order->amount_tax, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between text-sm font-bold text-odoo border-t border-gray-300 pt-2">
                    <span>Total Tagihan:</span>
                    <strong>Rp{{ number_format($order->amount_total, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chatter / Audit Log (SR-12) -->
<div class="bg-white rounded border border-gray-200 shadow-sm p-6">
    <h3 class="font-bold text-gray-800 text-sm mb-4 pb-2 border-b border-gray-200 flex items-center gap-2">
        <span>📜</span> Jejak Audit Dokumen & Log Notes (SR-12)
    </h3>
    <div class="space-y-3">
        @forelse($auditLogs as $log)
            <div class="flex gap-3 text-xs">
                <div class="w-7 h-7 bg-purple-700 text-white rounded-full flex items-center justify-center font-bold text-[11px] shrink-0">
                    {{ substr($log->actor, 0, 1) }}
                </div>
                <div>
                    <div class="text-gray-500 text-[11px]">
                        <strong>{{ $log->actor }}</strong> ({{ $log->role }}) • {{ $log->timestamp }}
                    </div>
                    <div class="text-gray-800 mt-0.5">{{ $log->details }}</div>
                </div>
            </div>
        @empty
            <div class="text-xs text-gray-400 italic">Belum ada riwayat aktivitas tercatat.</div>
        @endforelse
    </div>
</div>
@endsection

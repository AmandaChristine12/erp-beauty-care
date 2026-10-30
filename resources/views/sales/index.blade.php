@extends('layouts.odoo')

@section('title', 'Quotation & Sales Orders - NusaGlow ERP')

@section('breadcrumbs')
    <span class="font-bold text-gray-700 text-base">Sales Orders & Quotations</span>
@endsection

@section('control-actions')
    <a href="{{ route('sales.create') }}" class="bg-odoo hover-bg-odoo text-white font-semibold px-3 py-1.5 rounded shadow text-xs flex items-center gap-1">
        <span>+</span> Buat Penawaran / SO
    </a>
    <a href="{{ route('sales.customers') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-1.5 rounded text-xs">
        Daftar Pelanggan (S-02)
    </a>
@endsection

@section('content')
<div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h2 class="font-bold text-gray-800 text-sm">Daftar Dokumen Penjualan (S-05)</h2>
            <p class="text-xs text-gray-500">Mencakup status penawaran Draft hingga Sales Order terkonfirmasi.</p>
        </div>
        <span class="text-xs bg-purple-100 text-purple-800 font-semibold px-2 py-1 rounded">
            Total {{ $orders->count() }} Dokumen
        </span>
    </div>

    <table class="w-full text-left text-xs">
        <thead class="bg-gray-100 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider">
            <tr>
                <th class="py-3 px-4">Nomor Dokumen</th>
                <th class="py-3 px-4">Tanggal</th>
                <th class="py-3 px-4">Pelanggan</th>
                <th class="py-3 px-4">Gudang</th>
                <th class="py-3 px-4 text-right">DPP (Untaxed)</th>
                <th class="py-3 px-4 text-right">PPN 11%</th>
                <th class="py-3 px-4 text-right">Total Tagihan</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4 font-bold text-purple-900">
                        <a href="{{ route('sales.show', $order->id) }}" class="hover:underline">
                            {{ $order->id }}
                        </a>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $order->date }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">
                        {{ $order->customer->name ?? $order->customer_id }}
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        <span class="bg-gray-100 px-2 py-0.5 rounded border border-gray-200 font-mono text-[11px]">
                            {{ $order->warehouse_code }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right text-gray-600">
                        Rp{{ number_format($order->amount_untaxed, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4 text-right text-gray-600">
                        Rp{{ number_format($order->amount_tax, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4 text-right font-bold text-gray-900">
                        Rp{{ number_format($order->amount_total, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($order->status === 'Confirmed')
                            <span class="bg-green-100 text-green-800 text-[11px] font-semibold px-2.5 py-0.5 rounded border border-green-200">
                                Confirmed
                            </span>
                        @elseif($order->status === 'Draft')
                            <span class="bg-amber-100 text-amber-800 text-[11px] font-semibold px-2.5 py-0.5 rounded border border-amber-200">
                                Quotation (Draft)
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-[11px] font-semibold px-2.5 py-0.5 rounded">
                                {{ $order->status }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="{{ route('sales.show', $order->id) }}" class="text-odoo font-semibold hover:underline">
                            Buka Form ➔
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="py-8 text-center text-gray-400">
                        Belum ada dokumen sales order. Klik tombol <strong>+ Buat Penawaran / SO</strong> di atas untuk membuat yang pertama!
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

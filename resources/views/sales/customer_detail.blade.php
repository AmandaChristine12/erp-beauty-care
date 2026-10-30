@extends('layouts.odoo')

@section('title', 'Customer ' . $customer->name . ' - NusaGlow ERP')

@section('breadcrumbs')
    <a href="{{ route('sales.customers') }}" class="text-gray-500 hover:text-gray-700">Customers</a>
    <span class="text-gray-400">/</span>
    <span class="font-bold text-gray-800">{{ $customer->name }} ({{ $customer->id }}) - S-02</span>
@endsection

@section('control-actions')
    <a href="{{ route('sales.create', ['customer_id' => $customer->id]) }}" class="bg-odoo hover-bg-odoo text-white font-semibold px-3 py-1.5 rounded shadow text-xs">
        + Buat Sales Order
    </a>
    <a href="{{ route('sales.customers') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-1.5 rounded text-xs">
        Daftar Kontak
    </a>
@endsection

@section('content')
<div class="bg-white rounded border border-gray-200 shadow-sm p-6 mb-6">
    <!-- Statusbar -->
    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-3 py-1 rounded bg-purple-100 text-purple-900 border border-purple-200">
                Data Master Customer
            </span>
        </div>
        <div class="flex items-center text-xs font-semibold">
            <span class="bg-odoo text-white px-3 py-1 rounded-l">Profil & Kontak</span>
            <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-r">Credit Terms</span>
        </div>
    </div>

    <!-- Smart Buttons -->
    <div class="flex justify-end gap-3 mb-6">
        <div class="bg-gray-50 border border-gray-200 rounded px-3 py-1.5 flex items-center gap-2 text-xs">
            <span class="text-base">📋</span>
            <div>
                <div class="font-bold text-gray-800">{{ $customer->salesOrders->count() }} Orders</div>
                <div class="text-[10px] text-gray-500 uppercase">Histori Penjualan</div>
            </div>
        </div>
        <div class="border rounded px-3 py-1.5 flex items-center gap-2 text-xs {{ $exposure['is_exceeded'] ? 'bg-red-50 border-red-200 text-red-900' : 'bg-green-50 border-green-200 text-green-900' }}">
            <span class="text-base">💳</span>
            <div>
                <div class="font-bold">Rp{{ number_format($exposure['remaining_credit'], 0, ',', '.') }}</div>
                <div class="text-[10px] uppercase font-semibold">Sisa Plafon Kredit</div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-odoo">{{ $customer->name }}</h1>
        <div class="flex items-center gap-2 mt-1">
            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-medium">{{ $customer->type }}</span>
            <span class="text-xs text-gray-500">• Kota {{ $customer->city }}</span>
        </div>
    </div>

    <!-- Wireframe S-02 Grid Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded border border-gray-200 mb-6 text-xs">
        <div class="space-y-4">
            <div>
                <label class="block font-bold text-gray-500 mb-1">Customer ID</label>
                <div class="font-mono font-bold text-sm text-gray-800">{{ $customer->id }}</div>
            </div>
            <div>
                <label class="block font-bold text-gray-500 mb-1">Kota (City)</label>
                <div class="text-gray-800 font-semibold">{{ $customer->city }}</div>
            </div>
            <div>
                <label class="block font-bold text-gray-500 mb-1">Payment Terms</label>
                <div class="text-gray-800 font-semibold">{{ $customer->payment_terms }}</div>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block font-bold text-gray-500 mb-1">Credit Limit (Plafon Kredit Maksimal)</label>
                <div class="text-sm font-bold text-gray-900">Rp{{ number_format($customer->credit_limit, 0, ',', '.') }}</div>
            </div>
            <div>
                <label class="block font-bold text-gray-500 mb-1">Opening Unpaid AR (Saldo Piutang Belum Lunas)</label>
                <div class="text-sm font-bold {{ $customer->opening_receivable > 0 ? 'text-red-600' : 'text-gray-700' }}">
                    Rp{{ number_format($customer->opening_receivable, 0, ',', '.') }}
                </div>
            </div>
            <div class="pt-2 border-t border-gray-200">
                <label class="block font-bold text-purple-900 mb-1">Total Credit Exposure Saat Ini (SR-08 / F-11)</label>
                <div class="text-sm font-extrabold {{ $exposure['is_exceeded'] ? 'text-red-600' : 'text-green-700' }}">
                    Rp{{ number_format($exposure['total_exposure'], 0, ',', '.') }}
                    <span class="text-[11px] font-normal text-gray-600 block">
                        (Unpaid AR: Rp{{ number_format($exposure['unpaid_ar'], 0, ',', '.') }} + Unbilled SO: Rp{{ number_format($exposure['unbilled_so'], 0, ',', '.') }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Fixture Switcher Buttons for Testing T-03 & T-04 -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">
            PILIH FIXTURE PELANGGAN UJI DOSEN (T-03 / T-04):
        </label>
        <div class="flex flex-wrap gap-2">
            @foreach($allCustomers as $ac)
                <a href="{{ route('sales.customer.detail', $ac->id) }}" 
                   class="px-3 py-1 rounded text-xs border {{ $ac->id === $customer->id ? 'bg-odoo text-white font-bold border-odoo' : 'bg-white hover:bg-gray-100 text-gray-700 border-gray-300' }}">
                    {{ $ac->id }} - {{ $ac->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Chatter / Audit Log (SR-12) -->
<div class="bg-white rounded border border-gray-200 shadow-sm p-6">
    <h3 class="font-bold text-gray-800 text-sm mb-4 pb-2 border-b border-gray-200 flex items-center gap-2">
        <span>📜</span> Log Notes & Riwayat Perubahan (SR-12)
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
            <div class="text-xs text-gray-400 italic">Belum ada catatan aktivitas khusus pada pelanggan ini.</div>
        @endforelse
    </div>
</div>
@endsection

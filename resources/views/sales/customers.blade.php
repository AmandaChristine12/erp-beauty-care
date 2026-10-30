@extends('layouts.odoo')

@section('title', 'Customers & Credit Terms - NusaGlow ERP')

@section('breadcrumbs')
    <span class="font-bold text-gray-700 text-base">Customers & Credit Terms (S-02 / F-03)</span>
@endsection

@section('control-actions')
    <a href="{{ route('sales.create') }}" class="bg-odoo hover-bg-odoo text-white font-semibold px-3 py-1.5 rounded shadow text-xs">
        + Buat Sales Order Baru
    </a>
@endsection

@section('content')
<div class="mb-4 bg-purple-50 border-l-4 border-purple-600 p-4 rounded text-xs text-purple-900">
    <strong>Aturan SR-08 & Fitur F-11:</strong> Plafon kredit mengendalikan eksposur piutang perusahaan. 
    <code>Credit Exposure = Unpaid AR + Unbilled Orders</code>. Pesanan baru akan otomatis diblokir jika total eksposur melampaui limit kredit pelanggan.
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($customers as $c)
        @php
            $exp = $customerExposures[$c->id];
        @endphp
        <div class="bg-white rounded border {{ $exp['is_exceeded'] ? 'border-red-300 ring-1 ring-red-400' : 'border-gray-200' }} shadow-sm p-4 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <span class="font-mono text-xs font-bold text-odoo bg-purple-50 px-2 py-0.5 rounded">{{ $c->id }}</span>
                    <h3 class="font-bold text-gray-800 text-sm mt-1">{{ $c->name }}</h3>
                    <span class="text-[11px] text-gray-500">{{ $c->type }} • {{ $c->city }}</span>
                </div>
                @if($exp['is_exceeded'])
                    <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded border border-red-200 uppercase">
                        Limit Terlampaui
                    </span>
                @else
                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded border border-green-200 uppercase">
                        Kredit Aman
                    </span>
                @endif
            </div>

            <div class="border-t border-gray-100 my-3 pt-2 space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Credit Limit:</span>
                    <strong class="text-gray-900">Rp{{ number_format($c->credit_limit, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Opening Unpaid AR:</span>
                    <strong class="{{ $c->opening_receivable > 0 ? 'text-red-600' : 'text-gray-700' }}">
                        Rp{{ number_format($c->opening_receivable, 0, ',', '.') }}
                    </strong>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Payment Terms:</span>
                    <span class="font-semibold text-gray-700">{{ $c->payment_terms }}</span>
                </div>
                <div class="flex justify-between text-xs pt-1.5 border-t border-gray-100">
                    <span class="font-semibold text-gray-700">Total Credit Exposure:</span>
                    <strong class="{{ $exp['is_exceeded'] ? 'text-red-600' : 'text-purple-900' }}">
                        Rp{{ number_format($exp['total_exposure'], 0, ',', '.') }}
                    </strong>
                </div>
            </div>

            <div class="mt-4 pt-2 border-t border-gray-100 flex gap-2">
                <a href="{{ route('sales.customer.detail', $c->id) }}" class="w-1/2 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-1.5 rounded text-xs">
                    Profil Lengkap
                </a>
                <a href="{{ route('sales.create', ['customer_id' => $c->id]) }}" class="w-1/2 text-center bg-odoo hover-bg-odoo text-white font-semibold py-1.5 rounded text-xs">
                    + Buat Order
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection

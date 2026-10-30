<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NusaGlow ERP - Beauty Care')</title>
    <!-- Tailwind CSS CDN for instant, beautiful Odoo-like styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --odoo-purple: #714B67;
            --odoo-purple-dark: #56394e;
            --odoo-teal: #00A09D;
            --odoo-bg: #f8f9fa;
        }
        .bg-odoo { background-color: var(--odoo-purple); }
        .text-odoo { color: var(--odoo-purple); }
        .border-odoo { border-color: var(--odoo-purple); }
        .hover-bg-odoo:hover { background-color: var(--odoo-purple-dark); }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 text-sm font-sans antialiased">

    <!-- Odoo 17 Signature Topbar -->
    <header class="bg-odoo text-white h-12 flex items-center justify-between px-4 sticky top-0 z-50 shadow-md">
        <div class="flex items-center space-x-4">
            <!-- App Switcher 9 Dots -->
            <button class="p-1.5 hover:bg-white/20 rounded flex flex-col justify-center items-center gap-0.5 w-8 h-8">
                <div class="grid grid-cols-3 gap-0.5">
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                    <span class="w-1 h-1 bg-white rounded-sm"></span>
                </div>
            </button>
            <div class="font-bold text-base tracking-wide flex items-center gap-2">
                <span>NusaGlow ERP</span>
                <span class="text-xs bg-white/20 px-2 py-0.5 rounded font-normal">Sales & CRM</span>
            </div>
            <nav class="hidden md:flex items-center space-x-1 pl-4 text-xs font-medium">
                <a href="{{ route('sales.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('sales.index') || request()->routeIs('sales.show') || request()->routeIs('sales.create') ? 'bg-white/25 text-white font-bold' : 'hover:bg-white/10 text-gray-200' }}">Orders / Quotations</a>
                <a href="{{ route('sales.customers') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('sales.customers') || request()->routeIs('sales.customer.detail') ? 'bg-white/25 text-white font-bold' : 'hover:bg-white/10 text-gray-200' }}">Customers & Credit Terms (S-02)</a>
            </nav>
        </div>

        <div class="flex items-center space-x-3 text-xs">
            <span class="hidden sm:inline text-purple-200 border-r border-white/20 pr-3">PT Arunika Beauty Nusantara</span>
            
            <!-- Role Switcher -->
            <div class="flex items-center gap-1.5 bg-white text-gray-800 px-2.5 py-1 rounded shadow-sm font-semibold">
                <span class="text-gray-500 font-normal">Role:</span>
                <span class="text-purple-900 font-bold">Kevin Mahendra (Sales)</span>
            </div>

            <!-- Tombol Wajib Reset Seed Baseline -->
            <form action="{{ route('system.reset-seed') }}" method="POST" onsubmit="return confirm('PERINGATAN UJI DOSEN: Yakin ingin mereset seluruh database kembali ke baseline seed resmi?');">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-2.5 py-1 rounded shadow text-xs flex items-center gap-1">
                    <span>↻</span> Reset Seed Baseline
                </button>
            </form>
        </div>
    </header>

    <!-- Sub-Navbar / Control Panel (Breadcrumb & Action Bar) -->
    <div class="bg-white border-b border-gray-200 px-6 py-3 flex flex-wrap justify-between items-center shadow-sm min-h-[50px]">
        <div class="flex items-center gap-2">
            @yield('breadcrumbs')
        </div>
        <div class="flex items-center gap-2">
            @yield('control-actions')
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 text-green-800 text-sm rounded shadow-sm flex items-center justify-between">
                <div>
                    <strong class="font-bold">Sukses:</strong> {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-red-800 text-sm rounded shadow-sm">
                <div class="font-bold text-red-900 mb-1">Peringatan Aturan Bisnis Dilanggar:</div>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 text-blue-800 text-sm rounded shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Footer -->
    <footer class="text-center py-4 text-xs text-gray-500">
        PT Arunika Beauty Nusantara • Sistem ERP Skenario 4 Minggu • Kelompok 4 Mahasiswa Sistem Informasi
    </footer>

</body>
</html>

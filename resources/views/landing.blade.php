<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Point of Sale modern untuk bisnis Kamu. Kelola produk, transaksi, laporan dan stok dalam satu platform.">
    <title>CALAPOS — CALARAYA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased leading-relaxed">

<!-- Navigasi bar -->
<nav class="fixed top-0 inset-x-0 z-50 bg-gray-50/85 backdrop-blur-md border-b border-gray-200 h-16">
    <div class="h-full flex items-center justify-between mx-auto max-w-6xl px-6">
        <a href="/" class="text-xl font-extrabold text-gray-900 tracking-tight">CALA<span class="text-blue-500">POS</span></a>
        <div class="flex items-center gap-2">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center text-sm font-semibold px-6 py-2.5 rounded-md transition bg-transparent text-gray-900 border border-gray-200 hover:bg-blue-50 hover:border-blue-600 hover:text-blue-600">Masuk</a>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="pt-36 pb-20 md:pt-40 md:pb-28 text-center px-6">
    <div class="mx-auto max-w-6xl">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-gray-900 max-w-3xl mx-auto mb-5 leading-tight">Sistem POS Modern untuk Bisnis Kamu</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-9 leading-relaxed">Kelola produk, transaksi, stok dan laporan dalam satu platform yang mudah digunakan. Cocok untuk warung, toko, kafe, barbershop dan semua jenis usaha.</p>
        <div class="flex gap-3 justify-center flex-wrap">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center font-semibold rounded-md transition bg-blue-600 text-white hover:bg-blue-700 px-8 py-3.5 text-base shadow-sm">Daftarkan Toko</a>
            <a href="#features" class="inline-flex items-center justify-center font-semibold rounded-md transition bg-transparent text-gray-900 border border-gray-200 hover:bg-blue-50 hover:border-blue-600 hover:text-blue-600 px-8 py-3.5 text-base shadow-sm">Lihat Fitur</a>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-20 bg-white border-y border-gray-200 px-6" id="features">
    <div class="mx-auto max-w-6xl">
        <div class="text-center mb-14">
            <div class="text-sm font-semibold text-blue-600 uppercase tracking-wider mb-3">Fitur</div>
            <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-gray-900 max-w-2xl mx-auto mb-3.5">Semua yang Kamu butuhkan dalam satu platform</h2>
            <p class="text-base text-gray-600 max-w-xl mx-auto">Fitur lengkap untuk mengelola bisnis kamu secara efisien dan profesional.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-7 rounded-lg border border-gray-200 bg-white transition hover:border-blue-200 hover:shadow-[0_4px_24px_rgba(37,99,235,0.06)] group">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4 transition group-hover:bg-blue-600 group-hover:text-white">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Point of Sale</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Kasir digital yang cepat dan mudah digunakan. Dukung barcode scanner dan numeric keypad.</p>
            </div>
            <div class="p-7 rounded-lg border border-gray-200 bg-white transition hover:border-blue-200 hover:shadow-[0_4px_24px_rgba(37,99,235,0.06)] group">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4 transition group-hover:bg-blue-600 group-hover:text-white">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Laporan Real-time</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Dashboard analitik dengan grafik penjualan harian, bulanan, produk terlaris, dan ringkasan pendapatan.</p>
            </div>
            <div class="p-7 rounded-lg border border-gray-200 bg-white transition hover:border-blue-200 hover:shadow-[0_4px_24px_rgba(37,99,235,0.06)] group">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4 transition group-hover:bg-blue-600 group-hover:text-white">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Manajemen Stok</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Pantau stok secara otomatis. Notifikasi stok menipis dan log perubahan stok lengkap.</p>
            </div>
            <div class="p-7 rounded-lg border border-gray-200 bg-white transition hover:border-blue-200 hover:shadow-[0_4px_24px_rgba(37,99,235,0.06)] group">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4 transition group-hover:bg-blue-600 group-hover:text-white">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Multi-Pembayaran</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Terima pembayaran tunai dan QRIS. Dapat diintegrasikan dengan payment gateway.</p>
            </div>
            <div class="p-7 rounded-lg border border-gray-200 bg-white transition hover:border-blue-200 hover:shadow-[0_4px_24px_rgba(37,99,235,0.06)] group">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4 transition group-hover:bg-blue-600 group-hover:text-white">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Voucher & Diskon</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Buat kode voucher dengan berbagai jenis diskon, batas penggunaan, dan periode berlaku.</p>
            </div>
            <div class="p-7 rounded-lg border border-gray-200 bg-white transition hover:border-blue-200 hover:shadow-[0_4px_24px_rgba(37,99,235,0.06)] group">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-4 transition group-hover:bg-blue-600 group-hover:text-white">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Multi-User & Peran</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Kelola akses kasir dan admin dengan sistem peran dan izin yang fleksibel.</p>
            </div>
        </div>
    </div>
</section>

@include('partials.carousel')

<!-- How it Works -->
<section class="py-20 px-6 bg-gray-50">
    <div class="mx-auto max-w-6xl">
        <div class="text-center mb-14">
            <div class="text-sm font-semibold text-blue-600 uppercase tracking-wider mb-3">Cara Kerja</div>
            <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-gray-900 max-w-2xl mx-auto mb-3.5">Mulai dalam 3 langkah mudah</h2>
            <p class="text-base text-gray-600 max-w-xl mx-auto">Tidak perlu instalasi atau konfigurasi rumit.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 space-y-4 md:space-y-0 text-center relative z-10 before:absolute md:before:top-1/2 md:before:left-[16%] md:before:right-[16%] md:before:-z-10 md:before:border-gray-200">
            <div class="p-6 mt-3">
                <div class="w-14 h-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-extrabold mx-auto mb-5 shadow-[0_0_0_8px_white] ring-8 ring-white">1</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Daftarkan Toko</h3>
                <p class="text-sm text-gray-600">Isi data toko dan buat akun pemilik. Gratis dan hanya butuh 1 menit.</p>
            </div>
            <div class="p-6 mt-3">
                <div class="w-14 h-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-extrabold mx-auto mb-5 shadow-[0_0_0_8px_white] ring-8 ring-white">2</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Tambahkan Produk</h3>
                <p class="text-sm text-gray-600">Input produk atau layanan Kamu lengkap dengan harga, stok dan kategori.</p>
            </div>
            <div class="p-6 mt-3">
                <div class="w-14 h-14 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-extrabold mx-auto mb-5 shadow-[0_0_0_8px_white] ring-8 ring-white">3</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Mulai Berjualan</h3>
                <p class="text-sm text-gray-600">Gunakan kasir digital untuk memproses transaksi dan pantau laporan penjualan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="py-20 bg-white border-y border-gray-200 px-6" id="pricing">
    <div class="mx-auto max-w-6xl">
        <div class="text-center mb-14">
            <div class="text-sm font-semibold text-blue-600 uppercase tracking-wider mb-3">Harga</div>
            <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-gray-900 max-w-2xl mx-auto mb-3.5">Pilih paket yang tepat untuk bisnis Kamu</h2>
            <p class="text-base text-gray-600 max-w-xl mx-auto">Mulai gratis, upgrade kapan saja sesuai kebutuhan.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:max-w-4xl max-w-sm mx-auto gap-8">
            <div class="p-8 rounded-2xl border border-gray-200 bg-white text-center shadow-sm">
                <div class="text-base font-bold text-gray-900 mb-2">Paket Bulanan</div>
                <div class="text-4xl font-extrabold tracking-tight text-gray-900 mb-1">Rp 50k <span class="text-base font-medium text-gray-400">/ bulan</span></div>
                <div class="text-sm text-gray-500 mb-8">Akses penuh ke semua fitur</div>
                <ul class="text-left mb-8 space-y-4">
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Point of Sale lengkap</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Manajemen Stok</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Laporan Real-time</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Multi-user / Kasir</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Voucher & Diskon</li>
                </ul>
                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center font-semibold rounded-md transition bg-transparent text-gray-900 border-2 border-gray-200 hover:bg-gray-50 px-6 py-3 text-sm">Coba Gratis 7 Hari</a>
            </div>
            <div class="p-8 rounded-2xl border-2 border-blue-600 bg-white text-center relative shadow-lg ring-4 ring-blue-50">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Populer</div>
                <div class="text-base font-bold text-gray-900 mb-2">Paket Tahunan</div>
                <div class="text-4xl font-extrabold tracking-tight text-gray-900 mb-1">Rp 550k <span class="text-base font-medium text-gray-400">/ tahun</span></div>
                <div class="text-sm text-gray-500 mb-8">Hemat Rp 50k, paling direkomendasikan</div>
                <ul class="text-left mb-8 space-y-4">
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Semua Fitur Bulanan</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Penghematan Biaya</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Gratis Pembaruan Sistem</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Dukungan Prioritas</li>
                    <li class="text-sm text-gray-600 flex items-center gap-3"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-5 h-5 text-blue-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Akses Stabil Penuh</li>
                </ul>
                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center font-semibold rounded-md transition bg-blue-600 text-white hover:bg-blue-700 shadow-sm px-6 py-3 text-sm">Coba Gratis 7 Hari</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 bg-gray-900 text-center px-6">
    <div class="mx-auto max-w-4xl text-white">
        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-4 text-white">Siap mengembangkan bisnis Kamu?</h2>
        <p class="text-lg text-gray-400 mb-10 max-w-2xl mx-auto">Daftar sekarang dan mulai gunakan POS modern dalam hitungan menit.</p>
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center font-semibold px-8 py-4 rounded-md transition bg-white text-gray-900 hover:bg-gray-100 text-base shadow-sm">Daftarkan Toko</a>
    </div>
</section>

<!-- Footer -->
<footer class="py-10 border-t border-gray-200 bg-gray-50 px-6">
    <div class="mx-auto max-w-6xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="text-sm text-gray-500 font-medium">&copy; {{ date('Y') }} Calapos. All rights reserved.</div>
        <div class="flex flex-wrap justify-center gap-6">
            <a href="#features" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Fitur</a>
            <a href="#pricing" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Harga</a>
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Masuk</a>
            <a href="{{ route('register') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Daftar</a>
        </div>
    </div>
</footer>

</body>
</html>

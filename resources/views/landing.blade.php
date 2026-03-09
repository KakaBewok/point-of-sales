<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Point of Sale modern untuk bisnis Anda. Kelola produk, transaksi, laporan, dan stok dalam satu platform.">
    <title>POSify — Sistem POS Modern untuk Bisnis Anda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #fafafa;
            --bg-card: #ffffff;
            --text-primary: #18181b;
            --text-secondary: #52525b;
            --text-muted: #a1a1aa;
            --border: #e4e4e7;
            --accent: #2563eb;
            --accent-hover: #1d4ed8;
            --accent-light: #eff6ff;
            --accent-subtle: #dbeafe;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Layout ─── */
        .container { max-width: 1120px; margin: 0 auto; padding: 0 24px; }
        .section { padding: 80px 0; }
        .section-alt { background: var(--bg-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }

        /* ─── Navbar ─── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(250, 250, 250, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            height: 64px;
        }
        .navbar .container {
            display: flex; align-items: center; justify-content: space-between; height: 100%;
        }
        .navbar-brand {
            font-size: 20px; font-weight: 800; color: var(--text-primary);
            text-decoration: none; letter-spacing: -0.5px;
        }
        .navbar-brand span { color: var(--accent); }
        .navbar-links { display: flex; align-items: center; gap: 8px; }
        .navbar-links a {
            text-decoration: none; font-size: 14px; font-weight: 500;
            color: var(--text-secondary); padding: 8px 16px; border-radius: 6px;
            transition: color 0.15s, background 0.15s;
        }
        .navbar-links a:hover { color: var(--text-primary); background: var(--accent-light); }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 600; text-decoration: none;
            padding: 10px 24px; border-radius: 6px; border: none;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-primary {
            background: var(--accent); color: #fff;
        }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-outline {
            background: transparent; color: var(--text-primary);
            border: 1px solid var(--border);
        }
        .btn-outline:hover { background: var(--accent-light); border-color: var(--accent); color: var(--accent); }
        .btn-lg { padding: 14px 32px; font-size: 15px; }

        /* ─── Hero ─── */
        .hero {
            padding: 140px 0 80px;
            text-align: center;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 500; color: var(--accent);
            background: var(--accent-light); padding: 6px 14px; border-radius: 20px;
            margin-bottom: 24px; border: 1px solid var(--accent-subtle);
        }
        .hero-badge svg { width: 14px; height: 14px; }
        .hero h1 {
            font-size: clamp(32px, 5vw, 52px);
            font-weight: 800; line-height: 1.1;
            letter-spacing: -1.5px; color: var(--text-primary);
            max-width: 720px; margin: 0 auto 20px;
        }
        .hero p {
            font-size: 17px; color: var(--text-secondary);
            max-width: 520px; margin: 0 auto 36px; line-height: 1.7;
        }
        .hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        /* ─── Features ─── */
        .section-header { text-align: center; margin-bottom: 56px; }
        .section-header .tag {
            font-size: 13px; font-weight: 600; color: var(--accent);
            text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px;
        }
        .section-header h2 {
            font-size: clamp(24px, 3vw, 36px);
            font-weight: 800; letter-spacing: -0.8px;
            max-width: 600px; margin: 0 auto 14px;
        }
        .section-header p {
            font-size: 16px; color: var(--text-secondary);
            max-width: 540px; margin: 0 auto;
        }
        .features-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .feature-card {
            padding: 28px; border-radius: 8px;
            border: 1px solid var(--border); background: var(--bg-card);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            border-color: var(--accent-subtle);
            box-shadow: 0 4px 24px rgba(37, 99, 235, 0.06);
        }
        .feature-icon {
            width: 40px; height: 40px; border-radius: 8px;
            background: var(--accent-light); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .feature-icon svg { width: 20px; height: 20px; }
        .feature-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }

        /* ─── Steps ─── */
        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .step-card { text-align: center; padding: 24px; }
        .step-number {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--accent); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800;
            margin: 0 auto 20px;
        }
        .step-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .step-card p { font-size: 14px; color: var(--text-secondary); }

        /* ─── Pricing ─── */
        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .pricing-card {
            padding: 32px; border-radius: 8px;
            border: 1px solid var(--border); background: var(--bg-card);
            text-align: center;
        }
        .pricing-card.popular {
            border-color: var(--accent); position: relative;
        }
        .pricing-card.popular::before {
            content: 'Populer'; position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            background: var(--accent); color: #fff; font-size: 12px; font-weight: 600;
            padding: 4px 14px; border-radius: 4px;
        }
        .pricing-card .plan-name { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .pricing-card .price { font-size: 36px; font-weight: 800; letter-spacing: -1px; margin-bottom: 4px; }
        .pricing-card .price span { font-size: 14px; font-weight: 500; color: var(--text-muted); }
        .pricing-card .price-note { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; }
        .pricing-card ul {
            list-style: none; text-align: left; margin-bottom: 28px;
        }
        .pricing-card ul li {
            font-size: 14px; color: var(--text-secondary);
            padding: 6px 0; display: flex; align-items: center; gap: 8px;
        }
        .pricing-card ul li svg { width: 16px; height: 16px; color: var(--accent); flex-shrink: 0; }

        /* ─── CTA ─── */
        .cta-section {
            text-align: center; padding: 80px 0;
            background: var(--text-primary);
        }
        .cta-section h2 {
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 800; color: #fff; letter-spacing: -0.5px; margin-bottom: 14px;
        }
        .cta-section p { font-size: 16px; color: #a1a1aa; margin-bottom: 32px; }
        .cta-section .btn-primary { background: #fff; color: var(--text-primary); }
        .cta-section .btn-primary:hover { background: #f4f4f5; }

        /* ─── Footer ─── */
        .footer {
            padding: 40px 0; border-top: 1px solid var(--border);
        }
        .footer .container {
            display: flex; justify-content: space-between; align-items: center;
        }
        .footer-text { font-size: 13px; color: var(--text-muted); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a {
            font-size: 13px; color: var(--text-muted); text-decoration: none;
            transition: color 0.15s;
        }
        .footer-links a:hover { color: var(--text-primary); }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .features-grid, .steps-grid, .pricing-grid {
                grid-template-columns: 1fr;
            }
            .navbar-links .hide-mobile { display: none; }
            .footer .container { flex-direction: column; gap: 16px; text-align: center; }
            .hero { padding: 120px 0 60px; }
        }
        @media (min-width: 769px) {
            .pricing-grid { grid-template-columns: repeat(2, 1fr); max-width: 800px; margin: 0 auto; }
        }
        @media (min-width: 769px) and (max-width: 1024px) {
            .features-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <a href="/" class="navbar-brand">CALA<span>POS</span></a>
        <div class="navbar-links">
            <a href="#features" class="hide-mobile">Fitur</a>
            <a href="#pricing" class="hide-mobile">Harga</a>
            <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Daftar Gratis</a>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="container">
        <div class="hero-badge">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Mulai gratis, tanpa kartu kredit
        </div>
        <h1>Sistem POS Modern untuk Bisnis Anda</h1>
        <p>Kelola produk, transaksi, stok, dan laporan dalam satu platform yang mudah digunakan. Cocok untuk toko, kafe, salon, dan semua jenis usaha.</p>
        <div class="hero-actions">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftarkan Toko Gratis</a>
            <a href="#features" class="btn btn-outline btn-lg">Lihat Fitur</a>
        </div>
    </div>
</section>

<!-- Features -->
<section class="section section-alt" id="features">
    <div class="container">
        <div class="section-header">
            <div class="tag">Fitur</div>
            <h2>Semua yang Anda butuhkan dalam satu platform</h2>
            <p>Fitur lengkap untuk mengelola bisnis Anda secara efisien dan profesional.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3>Point of Sale</h3>
                <p>Kasir digital yang cepat dan mudah digunakan. Dukung barcode scanner dan numeric keypad.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3>Laporan Real-time</h3>
                <p>Dashboard analitik dengan grafik penjualan harian, produk terlaris, dan ringkasan pendapatan.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3>Manajemen Stok</h3>
                <p>Pantau stok secara otomatis. Notifikasi stok menipis dan log perubahan stok lengkap.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3>QRIS & Multi-Pembayaran</h3>
                <p>Terima pembayaran tunai, QRIS, dan transfer bank. Integrasi Midtrans siap pakai.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <h3>Voucher & Diskon</h3>
                <p>Buat kode voucher dengan berbagai jenis diskon, batas penggunaan, dan periode berlaku.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3>Multi-User & Peran</h3>
                <p>Kelola akses kasir dan admin dengan sistem peran dan izin yang fleksibel.</p>
            </div>
        </div>
    </div>
</section>

<!-- How it Works -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="tag">Cara Kerja</div>
            <h2>Mulai dalam 3 langkah mudah</h2>
            <p>Tidak perlu instalasi atau konfigurasi rumit.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Daftarkan Toko</h3>
                <p>Isi nama toko dan buat akun pemilik. Gratis dan hanya butuh 1 menit.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Tambahkan Produk</h3>
                <p>Input produk atau layanan Anda lengkap dengan harga, stok, dan kategori.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Mulai Berjualan</h3>
                <p>Gunakan kasir digital untuk memproses transaksi dan pantau laporan penjualan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="section section-alt" id="pricing">
    <div class="container">
        <div class="section-header">
            <div class="tag">Harga</div>
            <h2>Pilih paket yang tepat untuk bisnis Anda</h2>
            <p>Mulai gratis, upgrade kapan saja sesuai kebutuhan.</p>
        </div>
        <div class="pricing-grid">
            <div class="pricing-card">
                <div class="plan-name">Paket Bulanan</div>
                <div class="price">Rp 50k <span>/ bulan</span></div>
                <div class="price-note">Akses penuh ke semua fitur</div>
                <ul>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Point of Sale lengkap</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Manajemen Stok</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Laporan Real-time</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Multi-user / Kasir</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Voucher & Diskon</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-outline" style="width:100%">Mulai Coba Gratis 7 Hari</a>
            </div>
            <div class="pricing-card popular">
                <div class="plan-name">Paket Tahunan</div>
                <div class="price">Rp 550k <span>/ tahun</span></div>
                <div class="price-note">Hemat Rp 50k, paling direkomendasikan</div>
                <ul>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Semua Fitur Bulanan</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Penghematan Biaya</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Gratis Pembaruan Sistem</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Dukungan Prioritas</li>
                    <li><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Akses Stabil Penuh</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-primary" style="width:100%">Mulai Coba Gratis 7 Hari</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Siap mengembangkan bisnis Anda?</h2>
        <p>Daftar sekarang dan mulai gunakan POS modern dalam hitungan menit.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftarkan Toko Gratis</a>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-text">&copy; {{ date('Y') }} POSify. All rights reserved.</div>
        <div class="footer-links">
            <a href="#features">Fitur</a>
            <a href="#pricing">Harga</a>
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}">Daftar</a>
        </div>
    </div>
</footer>

</body>
</html>

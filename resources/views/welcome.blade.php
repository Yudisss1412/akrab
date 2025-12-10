<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM AKRAB Banyuwangi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS Variables - sesuaikan dengan root variables dari layout app.blade.php */
        :root {
            --primary-color: #a8d5c9;
            --primary-color-dark: #006E5C;
            --secondary-color: #FFFFFF;
            --background-color: #f0fdfa;
            --dark-text-color: #333;
            --border-color: #e9ecef;

            /* Footer capsule sizing */
            --soc-icon: 18px;     /* ukuran ikon */
            --soc-gap: 12px;      /* jarak antar ikon */
            --soc-hitbox: 28px;   /* tap target per ikon */
            --soc-height: 36px;  /* tinggi kapsul */
        }

        /* Responsive adjustments for various screen sizes */

        /* Large desktop adjustments */
        @media (min-width: 1400px) {
            .intro-content {
                max-width: 1400px;
                margin: 0 auto;
            }

            .main-section {
                max-width: 1400px;
                margin: 0 auto;
                padding: 3rem 2rem;
            }
        }

        /* Tablet adjustments */
        @media (max-width: 992px) {
            .header {
                padding: 0 1.5rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .logo img {
                height: 45px;
                width: 45px;
            }

            .nav-links {
                display: none;
            }

            .hamburger-menu {
                display: block;
                margin-left: auto;
            }

            .auth-buttons {
                width: 100%;
                display: flex;
                justify-content: center;
                margin-top: 1rem;
                gap: 1rem;
            }

            .main-section {
                padding: 2.5rem 1.5rem;
            }

            .intro-content {
                flex-direction: column;
                gap: 2.5rem;
                text-align: center;
            }

            .hero-text {
                padding-right: 0;
            }

            .cta-buttons {
                justify-content: center;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }

            .tentang-container {
                flex-direction: column;
                gap: 2.5rem;
            }

            .tentang-left, .tentang-right {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 1rem;
            }

            .logo img {
                height: 40px;
                width: 40px;
            }

            .main-section {
                padding: 2rem 1rem;
            }

            .intro-content {
                gap: 2rem;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            h1 {
                font-size: 2.2rem;
                line-height: 1.2;
            }

            .hero-text p {
                font-size: 1.1rem;
            }

            .section-header h2 {
                font-size: 2rem;
            }

            .cta-button {
                padding: 0.9rem 1.8rem;
                font-size: 1rem;
            }
        }

        /* Mobile adjustments */
        @media (max-width: 576px) {
            .header {
                padding: 0.8rem 0.8rem;
            }

            .logo img {
                height: 35px;
                width: 35px;
            }

            .auth-buttons {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }

            .auth-buttons a {
                width: 100%;
                text-align: center;
                padding: 0.7rem 1rem;
                font-size: 0.9rem;
            }

            .main-section {
                padding: 1.8rem 1rem;
            }

            .intro-content {
                padding: 0 0.5rem;
                gap: 1.8rem;
            }

            h1 {
                font-size: 1.8rem;
                line-height: 1.3;
            }

            .hero-text p {
                font-size: 1rem;
                line-height: 1.6;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 0.8rem;
            }

            .cta-button {
                width: 100%;
                padding: 0.85rem 1.5rem;
                font-size: 0.95rem;
            }

            .section-header h2 {
                font-size: 1.6rem;
            }

            .section-header p {
                font-size: 0.95rem;
            }

            .product-grid {
                gap: 1.5rem;
            }

            .product-card img {
                height: 200px;
                object-fit: cover;
            }

            .tentang-headline {
                font-size: 1.8rem;
            }

            .tentang-content-title {
                font-size: 1.1rem;
            }

            .tentang-support-title {
                font-size: 1rem;
            }
        }

        /* Small mobile adjustments */
        @media (max-width: 400px) {
            .header {
                padding: 0.6rem 0.6rem;
            }

            .logo img {
                height: 32px;
                width: 32px;
            }

            .main-section {
                padding: 1.5rem 0.8rem;
            }

            h1 {
                font-size: 1.6rem;
            }

            .hero-text p {
                font-size: 0.95rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .product-card img {
                height: 180px;
            }

            .product-card h3 {
                font-size: 1rem;
            }

            .price {
                font-size: 0.95rem;
            }

            .view-product-btn {
                font-size: 0.85rem;
                padding: 0.4rem 0.8rem;
            }

            .tentang-headline {
                font-size: 1.6rem;
            }

            .social-icons-background {
                width: 130px;
                height: 30px;
                padding: 0 8px;
            }

            .social-icon svg {
                width: 18px;
                height: 18px;
            }
        }

        @media (max-width: 360px) {
            h1 {
                font-size: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.4rem;
            }

            .hero-text p {
                font-size: 0.9rem;
            }

            .cta-button {
                padding: 0.75rem 1.2rem;
                font-size: 0.9rem;
            }

            .product-card img {
                height: 160px;
            }

            .tentang-headline {
                font-size: 1.5rem;
            }
        }

        /* Mobile menu default state */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 78px;
            left: 0;
            right: 0;
            background: white;
            z-index: 999;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex-direction: column;
            gap: 0.5rem;
        }

        .mobile-nav a {
            display: block;
            padding: 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            border: 1px solid #e9ecef;
        }

        .mobile-nav a {
            display: block;
            padding: 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            border: 1px solid #e9ecef;
        }

        .mobile-nav .mobile-link {
            display: block;
            padding: 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            border: 1px solid #e9ecef;
        }

        .hamburger-menu {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 30px;
            height: 30px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            box-sizing: border-box;
        }

        .hamburger-line {
            width: 100%;
            height: 3px;
            background: #006E5C;
            border-radius: 10px;
            transition: all 0.3s;
            position: relative;
        }

        .hamburger-line {
            width: 100%;
            height: 3px;
            background: #006E5C;
            border-radius: 10px;
            transition: all 0.3s;
            position: relative;
        }

        /* Product card styling for better responsiveness */
        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .product-info {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0; /* Izinkan elemen untuk menyusut */
        }

        .product-info h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
            color: #333;
            word-wrap: break-word; /* Mencegah overflow judul produk */
            overflow-wrap: break-word;
            hyphens: auto;
        }

        .price {
            font-weight: 700;
            color: #006E5C;
            margin: 0 0 0.8rem 0;
            font-size: 1.1rem;
        }

        .view-product-btn {
            background: #006E5C;
            color: white;
            border: none;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
            align-self: flex-start;
        }

        .view-product-btn:hover {
            background: #a8d5c9;
            color: #006E5C;
        }

        /* Section header styling */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.2rem;
            color: #006E5C;
            margin: 0 0 0.5rem 0;
        }

        .section-header p {
            font-size: 1.1rem;
            color: #666;
            margin: 0;
        }

        /* Tentang section styling */
        .tentang-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .tentang-left, .tentang-right {
            width: 100%;
        }

        .tentang-subtitle {
            color: #006E5C;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
            text-transform: uppercase;
        }

        .tentang-headline {
            font-size: 2.5rem;
            line-height: 1.2;
            margin: 0 0 1.5rem 0;
            color: #333;
            word-break: break-word; /* Mencegah overflow teks panjang */
            overflow-wrap: break-word;
        }

        .headline-inline {
            color: #006E5C;
        }

        .tentang-content-title {
            font-size: 1.2rem;
            color: #006E5C;
            margin: 0 0 1rem 0;
            font-weight: 700;
        }

        .tentang-content-block p,
        .tentang-support-block p {
            font-size: 1rem;
            line-height: 1.6;
            color: #666;
            margin: 0 0 1rem 0;
        }

        .tentang-support-title {
            font-size: 1.1rem;
            color: #006E5C;
            margin: 1.5rem 0 0.5rem 0;
            font-weight: 700;
        }

        .gabung-link {
            display: inline-block;
            background: #006E5C;
            color: white !important;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .gabung-link:hover {
            background: #a8d5c9;
            color: #006E5C !important;
        }

        /* Footer styling - mengikuti struktur CSS dari layout app.blade.php */
        .footer{
            background-color: var(--secondary-color);
            color: var(--primary-color-dark);
            border-top: 1px solid var(--border-color);
            width: 100%; position: static; z-index: 100;
            font-size: 12px;
        }
        .footer .footer__inner.footer-3col{
            width: 100%; max-width: none; margin: 0;
            padding: 1rem 2rem;
            display: grid;
            grid-template-columns: minmax(0,1fr) auto minmax(0,1fr);
            align-items: center; gap: 1rem;
        }
        .footer-left{ justify-self: start; display:inline-flex; align-items:center; gap: 1rem; }
        .footer-center{ justify-self: center; text-align:center; }
        .footer-right{ justify-self: end; display:flex; align-items:center; }

        /* Link kebijakan */
        .footer-privacy a {
            color: var(--primary-color-dark) !important;
            text-decoration: none !important;
            font-size: 12px !important;
            display: inline-block !important;
            transition: color 0.2s ease !important;
        }

        /* Memastikan link tidak mewarisi warna putih dari CSS global */
        .footer .footer-privacy a,
        footer .footer-privacy a {
            color: var(--primary-color-dark) !important;
        }

        /* Selector yang sangat spesifik untuk memastikan warna link tetap hijau */
        .footer .footer__inner .footer-right .footer-privacy a,
        footer .footer__inner .footer-right .footer-privacy a {
            color: var(--primary-color-dark) !important;
        }

        /* Memastikan link Kebijakan Privasi tetap hijau di semua kondisi */
        .footer-privacy a:link,
        .footer-privacy a:visited,
        .footer-privacy a:active {
            color: var(--primary-color-dark) !important;
            text-decoration: none !important;
        }

        .footer-privacy a:hover {
            text-decoration: underline !important;
            color: var(--primary-color) !important;
        }

        /* Kapsul sosmed */
        .social-icons-background{
            display: inline-flex; align-items: center; justify-content: center;
            gap: var(--soc-gap); height: var(--soc-height);
            min-width: calc(var(--soc-icon) * 6 + var(--soc-gap) * 3); /* cukup utk 4 ikon */
            padding: 0 10px; border-radius: 9999px;
            background: var(--primary-color-dark);
            color: #fff; line-height: 0;
        }
        .social-icon{
            width: var(--soc-hitbox); height: var(--soc-hitbox);
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%; line-height: 0;
        }
        .social-icon :is(svg,img,i){
            width: var(--soc-icon) !important; height: var(--soc-icon) !important;
            display: block !important; vertical-align: middle; object-fit: contain;
            fill: currentColor; stroke: none;
        }

        /* Guard svg footer */
        .footer svg{ max-width: none !important; transform: none !important; }

        /* ========== Responsive Footer ========== */
        @media (max-width: 640px){
            .footer .footer__inner.footer-3col{
                grid-template-columns: 1fr;
                row-gap: .75rem; text-align: center;
            }
            .footer-left, .footer-center, .footer-right{ justify-self: center; }
        }

        /* ============================================================
           FOOTER — LOCKED LAYOUT (© kiri • sosmed center • Privasi kanan)
           ============================================================ */
        @media (min-width:768px){
            footer.footer{ position:relative !important; min-height:64px !important; }

            /* pastikan inner bukan anchor */
            footer.footer .footer__inner{ position:static !important; }

            /* KIRI: © */
            footer.footer .footer-left{
                position:absolute !important;
                left:2rem !important;
                top:50% !important;
                transform:translateY(-50%) !important;
                margin:0 !important;
                white-space:nowrap !important;
                display:flex !important; align-items:center !important;
            }

            /* TENGAH: kapsul sosmed */
            footer.footer .footer-center,
            footer.footer .social-icons-background{
                position:absolute !important;
                left:50% !important;
                top:50% !important;
                transform:translate(-50%,-50%) !important;
                z-index:2 !important;
                margin:0 !important;
                text-align:center !important;
            }

            /* KANAN: kebijakan privasi */
            footer.footer .footer-right,
            footer.footer .footer-privacy{
                position:absolute !important;
                right:2rem !important;
                top:50% !important;
                transform:translateY(-50%) !important;
                margin:0 !important;
                text-align:right !important;
                white-space:nowrap !important;
                display:flex !important; align-items:center !important;
            }
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            .nav-links {
                display: none !important;
            }

            .auth-buttons {
                display: none !important;
            }

            .hamburger-menu {
                display: flex !important;
            }
        }

        /* Desktop styles */
        @media (min-width: 769px) {
            .mobile-nav {
                display: none;
            }

            .hamburger-menu {
                display: none;
            }

            /* Show desktop navigation on larger screens */
            .nav-links {
                display: flex;
            }

            .auth-buttons {
                display: flex;
            }
        }

        /* Active hamburger menu styling */
        .hamburger-menu.active .hamburger-line:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }

        .hamburger-menu.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .hamburger-menu.active .hamburger-line:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }

        /* Override dan perbaikan untuk mencegah overflow di mobile */
        .price {
            word-wrap: break-word;
            overflow-wrap: break-word;
            font-size: 1rem;
        }

        .view-product-btn {
            align-self: flex-start;
            word-wrap: break-word;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .section-header h2 {
            word-break: break-word;
            hyphens: auto;
        }

        /* Perbaikan untuk product grid di mobile kecil */
        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr !important;
            }

            .product-card {
                margin: 0 auto;
                max-width: 100%;
            }

            .product-card img {
                height: 180px;
            }

            .product-info h3 {
                font-size: 1rem;
                line-height: 1.3;
            }

            .price {
                font-size: 0.95rem;
            }

            .view-product-btn {
                font-size: 0.8rem;
                padding: 0.5rem 0.9rem;
            }
        }

        @media (max-width: 360px) {
            .product-card img {
                height: 160px;
            }

            .product-info h3 {
                font-size: 0.9rem;
            }

            .price {
                font-size: 0.9rem;
            }

            .view-product-btn {
                font-size: 0.75rem;
                padding: 0.45rem 0.8rem;
                align-self: flex-start;
            }

            .gabung-link {
                font-size: 0.8rem;
                padding: 0.5rem 0.9rem;
                background: #006E5C !important;
                color: white !important;
            }
        }

        /* Perbaikan untuk teks panjang di section Tentang */
        .tentang-content-block p,
        .tentang-support-block p {
            word-break: break-word;
            hyphens: auto;
        }

        /* Perbaikan untuk cta buttons */
        .cta-button {
            max-width: 100%;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .cta-button {
                white-space: normal;
                text-align: center;
                font-size: 0.95rem;
                padding: 0.8rem 1.2rem;
            }

            .cta-button.primary, .cta-button.secondary {
                width: 100%;
            }
        }

        /* Hapus styling yang terkait dengan footer lama */
        .footer-privacy a {
            color: white !important; /* Tetap jaga warna link privacy policy di footer */
        }

        .footer-copyright {
            color: white !important; /* Tetap jaga warna copyright di footer */
        }
    </style>
</head>
<body>
    
    <header class="header">
        <a href="#" class="logo">
            <img src="src/Logo_UMKM.png" alt="Logo UMKM">
        </a>
        <nav class="nav-links">
            <a href="#intro" class="active">Intro</a>
            <a href="#populer">Populer</a>
            <a href="#tentang">Tentang</a>
        </nav>
        <div class="auth-buttons">
            <a href="/login" class="login">Login</a>
            <a href="/register" class="register">Register</a>
        </div>
         <button class="hamburger-menu" id="hamburger-btn" aria-label="Buka menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </header>

    <div class="mobile-nav" id="mobile-nav">
        <a href="#intro" class="mobile-link">Intro</a>
        <a href="#populer" class="mobile-link">Populer</a>
        <a href="#tentang" class="mobile-link">Tentang</a>
    </div>

    <main>
        <section id="intro" class="main-section">
            <div class="intro-content">
                <div class="hero-text animate-on-scroll">
                    <h1>Temukan & Dukung Produk Lokal Terbaik.</h1>
                    <p>Jelajahi ribuan produk UMKM berkualitas dari seluruh penjuru Indonesia. Belanja aman, mudah, dan langsung dari para pengrajin lokal.</p>
                    <div class="cta-buttons">
                        <a href="/login" class="cta-button primary">Masuk</a>
                        <a href="/register" class="cta-button secondary">Daftar Sekarang</a>
                    </div>
                </div>
                <div class="hero-image-container animate-on-scroll delay-1">
                    <img src="src/bg_landing_page.png" 
                         alt="Ilustrasi orang berbelanja produk lokal"
                         onerror="this.onerror=null;this.src='https://placehold.co/600x400/d4e9e2/006E5C?text=UMKM+AKRAB';">
                </div>
            </div>
        </section>

        <section id="populer" class="main-section">
            <div class="section-header animate-on-scroll">
                <h2>Produk Populer</h2>
                <p>Lihat produk yang paling banyak diminati oleh pelanggan kami.</p>
            </div>
            <div class="product-grid">
                <article class="product-card animate-on-scroll" 
                        data-title="Batik Gajah Uling" 
                        data-price="Rp 50.000" 
                        data-desc="Kain batik premium dengan motif khas Banyuwangi yang melegenda."
                        data-images="src/product_1.png,https://placehold.co/600x800/eee/333?text=Detail+1,https://placehold.co/600x800/ddd/333?text=Detail+2"
                        data-specs="Ukuran:2m x 1.15m|Warna:Hijau Botol|Bahan:Katun Primisima|Stok:Tersedia"
                        data-product-slug="batik-gajah-uling-01">
                    <img src="src/product_1.png" alt="Produk Batik">
                    <div class="product-info">
                        <h3>Batik Gajah Uling</h3>
                        <p class="price">Rp 50.000</p>
                        <button class="view-product-btn">Pratinjau</button>
                    </div>
                </article>
                <article class="product-card animate-on-scroll delay-1" 
                        data-title="Kopi Lanang Ijen" 
                        data-price="Rp 75.000" 
                        data-desc="Kopi robusta pilihan dari lereng Ijen, diproses secara tradisional."
                        data-images="https://placehold.co/400x400/333/fff?text=Kopi,https://placehold.co/600x800/eee/333?text=Biji+Kopi,https://placehold.co/600x800/ddd/333?text=Kemasan"
                        data-specs="Berat:250g|Jenis:Robusta|Proses:Natural|Asal:Lereng Ijen"
                        data-product-slug="kopi-lanang-ijen-02">
                    <img src="https://placehold.co/400x400/333/fff?text=Kopi" alt="Produk Kopi">
                    <div class="product-info">
                        <h3>Kopi Lanang Ijen</h3>
                        <p class="price">Rp 75.000</p>
                        <button class="view-product-btn">Pratinjau</button>
                    </div>
                </article>
            </div>
        </section>
        
        <section id="tentang" class="main-section">
            <div class="tentang-container">
                <div class="tentang-left animate-on-scroll">
                    <div class="tentang-title-block">
                        <p class="tentang-subtitle">Tentang Kami</p>
                        <h2 class="tentang-headline">
                            PAGUYUBAN<br>
                            <span class="headline-inline">UMKM AKRAB</span><br>
                            BANYUWANGI
                        </h2>
                    </div>
                    <div class="tentang-content-block">
                        <h3 class="tentang-content-title">VISI & MISI KAMI</h3>
                        <p>Menjadi wadah UMKM kreatif yang berdaya saing tinggi, berbasis kearifan lokal, dan mampu menembus pasar digital secara berkelanjutan.</p>
                    </div>
                </div>
                <div class="tentang-right animate-on-scroll delay-1">
                    <div class="tentang-content-block">
                        <h3 class="tentang-content-title">CERITA KAMI</h3>
                        <p>Kami adalah komunitas pelaku usaha kecil dan menengah yang tergabung dalam Paguyuban UMKM AKRAB. Berdiri dari semangat gotong royong, kami menghadirkan beragam produk lokal berkualitas.</p>
                    </div>
                    <div class="tentang-support-block">
                        <h3 class="tentang-support-title">Dukung Produk Lokal, Yuk</h3>
                        <p>Daftar sekarang dan mulai belanja langsung dari UMKM Banyuwangi</p>
                        <a href="/register" class="gabung-link">Gabung Sekarang</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <div class="footer__inner footer-3col">
            <!-- Kiri: Copyright -->
            <div class="footer-left">
                &copy; 2025 UMKM AKRAB.
            </div>

            <!-- Tengah: Ikon Sosial Media -->
            <div class="footer-center">
                <div class="social-icons-background">
                    <a href="https://www.instagram.com/akrab_banyuwangi" class="social-icon" aria-label="Instagram">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.8 2H16.2C19.4 2 22 4.6 22 7.8V16.2C22 17.7383 21.3889 19.2135 20.3012 20.3012C19.2135 21.3889 17.7383 22 16.2 22H7.8C4.6 22 2 19.4 2 16.2V7.8C2 6.26174 2.61107 4.78649 3.69878 3.69878C4.78649 2.61107 6.26174 2 7.8 2ZM7.6 4C6.64522 4 5.72955 4.37928 5.05442 5.05442C4.37928 5.72955 4 6.64522 4 7.6V16.4C4 18.39 5.61 20 7.6 20H16.4C17.3548 20 18.2705 19.6207 18.9456 18.9456C19.6207 18.2705 20 17.3548 20 16.4V7.6C20 5.61 18.39 4 16.4 4H7.6ZM17.25 5.5C17.5815 5.5 17.8995 5.6317 18.1339 5.86612C18.3683 6.10054 18.5 6.41848 18.5 6.75C18.5 7.08152 18.3683 7.39946 18.1339 7.63388C17.8995 7.8683 17.5815 8 17.25 8C16.9185 8 16.6005 7.8683 16.3661 7.63388C16.1317 7.39946 16 7.08152 16 6.75C16 6.41848 16.1317 6.10054 16.3661 5.86612C16.6005 5.6317 16.9185 5.5 17.25 5.5ZM12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 13.3261 16.4732 14.5979 15.5355 15.5355C14.5979 16.4732 13.3261 17 12 17C10.6739 17 9.40215 16.4732 8.46447 15.5355C7.52678 14.5979 7 13.3261 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7ZM12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9Z" fill="#F5F5F5"/>
                        </svg>
                    </a>
                    <a href="https://www.facebook.com/akrabbanyuwangi?_rdc=1&_rdr#" class="social-icon" aria-label="Facebook">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 12C22 6.48 17.52 2 12 2C6.48 2 2 6.48 2 12C2 16.84 5.44 20.87 10 21.8V15H8V12H10V9.5C10 7.57 11.57 6 13.5 6H16V9H14C13.45 9 13 9.45 13 10V12H16V15H13V21.95C18.05 21.45 22 17.19 22 12Z" fill="#F5F5F5"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="WhatsApp">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.0498 4.91005C18.1329 3.98416 17.0408 3.25002 15.8373 2.75042C14.6338 2.25081 13.3429 1.99574 12.0398 2.00005C6.5798 2.00005 2.1298 6.45005 2.1298 11.9101C2.1298 13.6601 2.5898 15.3601 3.4498 16.8601L2.0498 22.0001L7.2998 20.6201C8.7498 21.4101 10.3798 21.8301 12.0398 21.8301C17.4998 21.8301 21.9498 17.3801 21.9498 11.9201C21.9498 9.27005 20.9198 6.78005 19.0498 4.91005ZM12.0398 20.1501C10.5598 20.1501 9.1098 19.7501 7.8398 19.0001L7.5398 18.8201L4.4198 19.6401L5.2498 16.6001L5.0498 16.2901C4.22735 14.9771 3.79073 13.4593 3.7898 11.9101C3.7898 7.37005 7.4898 3.67005 12.0298 3.67005C14.2298 3.67005 16.2998 4.53005 17.8498 6.09005C18.6174 6.85392 19.2257 7.7626 19.6394 8.76338C20.0531 9.76417 20.264 10.8371 20.2598 11.9201C20.2798 16.4601 16.5798 20.1501 12.0398 20.1501ZM16.5598 13.9901C16.3098 13.8701 15.0898 13.2701 14.8698 13.1801C14.6398 13.1001 14.4798 13.0601 14.3098 13.3001C14.1398 13.5501 13.6698 14.1101 13.5298 14.2701C13.3898 14.4401 13.2398 14.4601 12.9898 14.3301C12.7398 14.2101 11.9398 13.9401 10.9998 13.1001C10.2598 12.4401 9.7698 11.6301 9.6198 11.3801C9.4798 11.1301 9.5998 11.0001 9.7298 10.8701C9.8398 10.7601 9.9798 10.5801 10.0998 10.4401C10.2198 10.3001 10.2698 10.1901 10.3498 10.0301C10.4298 9.86005 10.3898 9.72005 10.3298 9.60005C10.2698 9.48005 9.7698 8.26005 9.5698 7.76005C9.3698 7.28005 9.1598 7.34005 9.0098 7.33005H8.5298C8.3598 7.33005 8.0998 7.39005 7.8698 7.64005C7.6498 7.89005 7.0098 8.49005 7.0098 9.71005C7.0098 10.9301 7.8998 12.1101 8.0198 12.2701C8.1398 12.4401 9.7698 14.9401 12.2498 16.0101C12.8398 16.2701 13.2998 16.4201 13.6598 16.5301C14.2498 16.7201 14.7898 16.6901 15.2198 16.6301C15.6998 16.5601 16.6898 16.0301 16.8898 15.4501C17.0998 14.8701 17.0998 14.3801 17.0298 14.2701C16.9598 14.1601 16.8098 14.1101 16.5598 13.9901Z" fill="#F5F5F5"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Email">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM20 18H4V8L12 13L20 8V18Z" fill="#F5F5F5"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Kanan: Tautan Kebijakan -->
            <div class="footer-right">
                <div class="footer-privacy">
                    <a href="#">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    <div id="product-modal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <header class="modal-header">
                <h2 id="modal-title">Nama Produk</h2>
                <button id="modal-close-btn" aria-label="Tutup">&times;</button>
            </header>
            <div class="modal-body">
                <div class="modal-gallery">
                    <div class="modal-main-image-wrapper" id="modal-image-wrapper">
                        <img src="https://placehold.co/600x800/eee/333?text=Produk" alt="Detail Produk" id="modal-main-image">
                    </div>
                    <div class="modal-thumbnails" id="modal-thumbnails"></div>
                </div>
                <div class="modal-details">
                    <div class="price-status">
                        <h3 id="modal-price">Rp 0</h3>
                    </div>
                    <p class="short-desc" id="modal-short-desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <div class="specs">
                        <h4>Spesifikasi</h4>
                        <ul id="modal-specs"></ul>
                    </div>
                    <a href="/register" class="cta-button modal-cta" id="modal-register-cta">Daftar untuk Membeli</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const mobileNav = document.getElementById('mobile-nav');
            const body = document.body;

            if (hamburgerBtn && mobileNav) {
                hamburgerBtn.addEventListener('click', function() {
                    // Toggle mobile nav visibility
                    const isHidden = mobileNav.classList.contains('hidden') || mobileNav.style.display === 'none';

                    if (isHidden) {
                        mobileNav.style.display = 'flex';
                        hamburgerBtn.classList.add('active');
                        // Prevent body scroll when menu is open
                        body.style.overflow = 'hidden';
                    } else {
                        mobileNav.style.display = 'none';
                        hamburgerBtn.classList.remove('active');
                        // Restore body scroll
                        body.style.overflow = 'auto';
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInsideHeader = hamburgerBtn.contains(event.target);
                    const isClickInsideNav = mobileNav.contains(event.target);

                    if (!isClickInsideHeader && !isClickInsideNav && mobileNav.style.display === 'flex') {
                        mobileNav.style.display = 'none';
                        hamburgerBtn.classList.remove('active');
                        body.style.overflow = 'auto';
                    }
                });

                // Close mobile menu when window is resized to desktop size
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 769) {
                        mobileNav.style.display = 'none';
                        hamburgerBtn.classList.remove('active');
                        body.style.overflow = 'auto';
                    }
                });

                // Close mobile menu when clicking on any link inside it
                const mobileLinks = document.querySelectorAll('#mobile-nav a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileNav.style.display = 'none';
                        hamburgerBtn.classList.remove('active');
                        body.style.overflow = 'auto';
                    });
                });
            }

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;

                    e.preventDefault();

                    const target = document.querySelector(href);
                    if (target) {
                        const offsetTop = target.offsetTop - 100; // Adjust for header height

                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });

                        // Close mobile menu if open
                        mobileNav.style.display = 'none';
                        hamburgerBtn.classList.remove('active');
                        body.style.overflow = 'auto';
                    }
                });
            });
        });

        // Animation on scroll functionality
        document.addEventListener('DOMContentLoaded', function() {
            const animateOnScrollElements = document.querySelectorAll('.animate-on-scroll');

            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            animateOnScrollElements.forEach(element => {
                observer.observe(element);
            });
        });
    </script>
    <script src="js/script.js"></script>
</body>
</html>

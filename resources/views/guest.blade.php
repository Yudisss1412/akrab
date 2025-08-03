<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM AKRAB Banyuwangi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <header id="header" class="header fixed-top">
        <div class="branding d-flex align-items-center">
            <div class="container position-relative d-flex align-items-center justify-content-between">
                <a href="index.html" class="logo d-flex align-items-center">
                    <!-- Uncomment the line below if you also wish to use an image logo -->
                    <!-- <img src="assets/img/logo.png" alt=""> -->
                    <h1 class="sitename">Impact</h1>
                    <span>.</span>
                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="#hero" class="active">Home<br></a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#portfolio">Portfolio</a></li>
                        <li><a href="#team">Team</a></li>
                        <li><a href="blog.html">Blog</a></li>
                        <li class="dropdown"><a href="#"><span>Dropdown</span> <i
                                    class="bi bi-chevron-down toggle-dropdown"></i></a>
                            <ul>
                                <li><a href="#">Dropdown 1</a></li>
                                <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i
                                            class="bi bi-chevron-down toggle-dropdown"></i></a>
                                    <ul>
                                        <li><a href="#">Deep Dropdown 1</a></li>
                                        <li><a href="#">Deep Dropdown 2</a></li>
                                        <li><a href="#">Deep Dropdown 3</a></li>
                                        <li><a href="#">Deep Dropdown 4</a></li>
                                        <li><a href="#">Deep Dropdown 5</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Dropdown 2</a></li>
                                <li><a href="#">Dropdown 3</a></li>
                                <li><a href="#">Dropdown 4</a></li>
                            </ul>
                        </li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="hero" class="hero section accent-background">
            <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-5 justify-content-between">
                    <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                        <h2><span>Welcome to </span><span class="accent">Impact</span></h2>
                        <p>Sed autem laudantium dolores. Voluptatem itaque ea consequatur eveniet. Eum quas beatae
                            cumque eum quaerat.</p>
                        <div class="d-flex">
                            <a href="#about" class="btn-get-started">Get Started</a>
                            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8"
                                class="glightbox btn-watch-video d-flex align-items-center"><i
                                    class="bi bi-play-circle"></i><span>Watch Video</span></a>
                        </div>
                    </div>
                    <div class="col-lg-5 order-1 order-lg-2">
                        <img src="{{ asset('images/image1.png') }}" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </section>
        <!-- /Hero Section -->
        <!-- about section -->
        <section id="about" class="about_section section light-background">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="img-box">
                            <img src="{{ asset('images/1.jpg') }}" alt="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box text-dark">
                            <div class="heading_container">
                                <h2>
                                    About Our Apartment
                                </h2>
                            </div>
                            <p>
                                There are many variations of passages of Lorem Ipsum available, but the majority have
                                suffered alteration
                                in
                                some form, by injected humour, or randomised words
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end about section -->

        <!-- product section -->
        <section id="product" class="product section accent-background">
            <div class="container section-title" data-aos="fade-up">
                <h2>Populer Produk</h2>
            </div>
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center">
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/1.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/2.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/3.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/4.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/1.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/2.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/3.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="imgBox">
                            <img src="{{ asset('images/4.jpg') }}">
                        </div>
                        <div class="details">
                            <div class="textContent">
                                <h3>Chair</h3>
                                <div class="price">£250</div>
                            </div>
                            <button>Buy It Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- product section -->
        <!-- visi misi section -->
        <div class="testimonial-section section fix light-background">
            <div class="testimonial-wrapper style3">
                <div class="container">
                    <div class="section-title-white pb-0" data-aos="fade-up">
                        <h2 class="text-dark">Populer Produk</h2>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="testimonial-card style3">
                                <div class="testimonial-body">
                                    <div class="fancy-box d-flex flex-column">
                                        <div class="item2">
                                            <h6>Visi</h6>
                                        </div>
                                        <p>
                                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Nisi error minus
                                            repellendus vel, dolorum dicta reiciendis eius eum a ea dolores, tempora
                                            laboriosam debitis pariatur voluptatibus assumenda expedita veritatis animi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="testimonial-card style3">
                                <div class="testimonial-body">
                                    <div class="fancy-box d-flex flex-column">
                                        <div class="item2">
                                            <h6>Misi</h6>
                                        </div>
                                        <p>
                                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Nisi error minus
                                            repellendus vel, dolorum dicta reiciendis eius eum a ea dolores, tempora
                                            laboriosam debitis pariatur voluptatibus assumenda expedita veritatis animi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end visi misi section -->

        <!-- cerita tentang kami section -->
        <section id="about" class="about_section section accent-background">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-box">
                            <div class="heading_container">
                                <h2>
                                    Cerita Kami
                                </h2>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Praesentium vel explicabo
                                inventore minus molestias quaerat nulla eligendi. Doloribus porro eum sapiente! Dolor
                                nobis modi cupiditate, facilis excepturi fugiat sed doloremque!
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="img-box">
                            <img src="{{ asset('images/1.jpg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="banner">
            <h1>Dukung Produk Lokal, Yuk!</h1>
            <p>Daftar sekarang dan mulai belanja langsung dari UMKM Banyuwangi</p>
            <a href="#daftar">Gabung Sekarang</a>
        </div>

    </main>

    <footer class="footer">
        <div class="footer-copyright">
            <span>&copy;</span> Akrab Banyuwangi
        </div>
        <div class="social-icons-container">
            <div class="social-icons-background">
                <a href="https://www.instagram.com/akrab_banyuwangi" target="_blank" class="social-icon"
                    aria-label="Instagram">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7.8 2H16.2C19.4 2 22 4.6 22 7.8V16.2C22 17.7383 21.3889 19.2135 20.3012 20.3012C19.2135 21.3889 17.7383 22 16.2 22H7.8C4.6 22 2 19.4 2 16.2V7.8C2 6.26174 2.61107 4.78649 3.69878 3.69878C4.78649 2.61107 6.26174 2 7.8 2ZM7.6 4C6.64522 4 5.72955 4.37928 5.05442 5.05442C4.37928 5.72955 4 6.64522 4 7.6V16.4C4 18.39 5.61 20 7.6 20H16.4C17.3548 20 18.2705 19.6207 18.9456 18.9456C19.6207 18.2705 20 17.3548 20 16.4V7.6C20 5.61 18.39 4 16.4 4H7.6ZM17.25 5.5C17.5815 5.5 17.8995 5.6317 18.1339 5.86612C18.3683 6.10054 18.5 6.41848 18.5 6.75C18.5 7.08152 18.3683 7.39946 18.1339 7.63388C17.8995 7.8683 17.5815 8 17.25 8C16.9185 8 16.6005 7.8683 16.3661 7.63388C16.1317 7.39946 16 7.08152 16 6.75C16 6.41848 16.1317 6.10054 16.3661 5.86612C16.6005 5.6317 16.9185 5.5 17.25 5.5ZM12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 13.3261 16.4732 14.5979 15.5355 15.5355C14.5979 16.4732 13.3261 17 12 17C10.6739 17 9.40215 16.4732 8.46447 15.5355C7.52678 14.5979 7 13.3261 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7ZM12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9Z"
                            fill="#F5F5F5" />
                    </svg>
                </a>
                <a href="https://web.facebook.com/akrabbanyuwangi" target="_blank" class="social-icon"
                    aria-label="Facebook">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M22 12C22 6.48 17.52 2 12 2C6.48 2 2 6.48 2 12C2 16.84 5.44 20.87 10 21.8V15H8V12H10V9.5C10 7.57 11.57 6 13.5 6H16V9H14C13.45 9 13 9.45 13 10V12H16V15H13V21.95C18.05 21.45 22 17.19 22 12Z"
                            fill="#F5F5F5" />
                    </svg>
                </a>
                <a href="https://wa.me/nomor" target="_blank" class="social-icon" aria-label="WhatsApp">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19.05 4.91005C18.1331 3.98416 17.041 3.25002 15.8375 2.75042C14.634 2.25081 13.3431 1.99574 12.04 2.00005C6.57999 2.00005 2.12999 6.45005 2.12999 11.9101C2.12999 13.6601 2.58999 15.3601 3.44999 16.8601L2.04999 22.0001L7.29999 20.6201C8.74999 21.4101 10.38 21.8301 12.04 21.8301C17.5 21.8301 21.95 17.3801 21.95 11.9201C21.95 9.27005 20.92 6.78005 19.05 4.91005ZM12.04 20.1501C10.56 20.1501 9.10999 19.7501 7.83999 19.0001L7.53999 18.8201L4.41999 19.6401L5.24999 16.6001L5.04999 16.2901C4.22754 14.9771 3.79091 13.4593 3.78999 11.9101C3.78999 7.37005 7.48999 3.67005 12.03 3.67005C14.23 3.67005 16.3 4.53005 17.85 6.09005C18.6176 6.85392 19.2259 7.7626 19.6396 8.76338C20.0533 9.76417 20.2642 10.8371 20.26 11.9201C20.28 16.4601 16.58 20.1501 12.04 20.1501ZM16.56 13.9901C16.31 13.8701 15.09 13.2701 14.87 13.1801C14.64 13.1001 14.48 13.0601 14.31 13.3001C14.14 13.5501 13.67 14.1101 13.53 14.2701C13.39 14.4401 13.24 14.4601 12.99 14.3301C12.74 14.2101 11.94 13.9401 11 13.1001C10.26 12.4401 9.76999 11.6301 9.61999 11.3801C9.47999 11.1301 9.59999 11.0001 9.72999 10.8701C9.83999 10.7601 9.97999 10.5801 10.1 10.4401C10.22 10.3001 10.27 10.1901 10.35 10.0301C10.43 9.86005 10.39 9.72005 10.33 9.60005C10.27 9.48005 9.76999 8.26005 9.56999 7.76005C9.36999 7.28005 9.15999 7.34005 9.00999 7.33005H8.52999C8.35999 7.33005 8.09999 7.39005 7.86999 7.64005C7.64999 7.89005 7.00999 8.49005 7.00999 9.71005C7.00999 10.9301 7.89999 12.1101 8.01999 12.2701C8.13999 12.4401 9.76999 14.9401 12.25 16.0101C12.84 16.2701 13.3 16.4201 13.66 16.5301C14.25 16.7201 14.79 16.6901 15.22 16.6301C15.7 16.5601 16.69 16.0301 16.89 15.4501C17.1 14.8701 17.1 14.3801 17.03 14.2701C16.96 14.1601 16.81 14.1101 16.56 13.9901Z"
                            fill="#F5F5F5" />
                    </svg>
                </a>
                <a href="mailto:dwianggara1412@gmail.com" target="_blank" class="social-icon" aria-label="Email">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM20 18H4V8L12 13L20 8V18Z"
                            fill="#F5F5F5" />
                    </svg>
                </a>
            </div>
        </div>
        <div class="footer-privacy">
            <a href="#">Kebijakan Privasi</a>
        </div>
    </footer>

    <div id="product-modal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <button id="modal-close-btn" class="modal-close-btn" aria-label="Tutup">&times;</button>
            <div class="modal-image-container">
                <img src="src/product_1.png" alt="Detail Produk" id="modal-image">
            </div>
            <div class="modal-details">
                <h3 id="modal-store" class="modal-store-name">Nama Toko</h3>
                <h2 id="modal-title" class="modal-product-name">Nama Produk</h2>
                <p id="modal-price" class="modal-product-price">Harga Produk</p>

                <div class="modal-rating">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z">
                        </path>
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z">
                        </path>
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z">
                        </path>
                    </svg>
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z">
                        </path>
                    </svg>
                    <svg viewBox="0 0 24 24" class="star-empty">
                        <path
                            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z">
                        </path>
                    </svg>
                </div>

                <div class="modal-description-box">
                    <p id="modal-description">Deskripsi produk akan muncul di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/all.min.js') }}"></script>
    <script>
        let navmenulinks = document.querySelectorAll('.navmenu a');

        function navmenuScrollspy() {
            navmenulinks.forEach(navmenulink => {
                if (!navmenulink.hash) return;
                let section = document.querySelector(navmenulink.hash);
                if (!section) return;
                let position = window.scrollY + 200;
                if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
                    document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
                    navmenulink.classList.add('active');
                } else {
                    navmenulink.classList.remove('active');
                }
            })
        }
        window.addEventListener('load', navmenuScrollspy);
        document.addEventListener('scroll', navmenuScrollspy);
    </script>
</body>

</html>

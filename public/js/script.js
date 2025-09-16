document.addEventListener('DOMContentLoaded', () => {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileNav = document.getElementById('mobile-nav');
    if (hamburgerBtn && mobileNav) {
        hamburgerBtn.addEventListener('click', () => {
            mobileNav.classList.toggle('open');
        });
    }

    const modal = document.getElementById('product-modal');
    if (modal) {
        const viewProductBtns = document.querySelectorAll('.view-product-btn');
        const modalOverlay = document.querySelector('.modal-overlay');
        const closeModalBtn = document.getElementById('modal-close-btn');
        
        const modalTitle = document.getElementById('modal-title');
        const modalMainImage = document.getElementById('modal-main-image');
        const modalThumbnails = document.getElementById('modal-thumbnails');
        const modalPrice = document.getElementById('modal-price');
        const modalShortDesc = document.getElementById('modal-short-desc');
        const modalSpecs = document.getElementById('modal-specs');
        // [TAMBAHAN] Mengambil tombol CTA di modal
        const modalRegisterCta = document.getElementById('modal-register-cta');

        const openModal = (card) => {
            if (!card) return;

            modalTitle.textContent = card.dataset.title || 'Nama Produk';
            modalPrice.textContent = card.dataset.price || 'Harga tidak tersedia';
            modalShortDesc.textContent = card.dataset.desc || 'Deskripsi singkat tidak tersedia.';
            
            // [TAMBAHAN] Logika untuk mengubah link register di modal
            const productSlug = card.dataset.productSlug;
            if (productSlug && modalRegisterCta) {
                modalRegisterCta.href = `/register?redirect_to=/produk/${productSlug}`;
            } else if (modalRegisterCta) {
                modalRegisterCta.href = '/register'; // Fallback jika tidak ada slug
            }

            const images = (card.dataset.images || '').split(',');
            modalThumbnails.innerHTML = ''; 

            if (images.length > 0 && images[0]) {
                modalMainImage.src = images[0];
                
                images.forEach((imgSrc, index) => {
                    const thumbWrapper = document.createElement('div');
                    thumbWrapper.className = 'modal-thumbnail';
                    if (index === 0) thumbWrapper.classList.add('active');
                    
                    const thumbImg = document.createElement('img');
                    thumbImg.src = imgSrc;
                    thumbImg.alt = `Thumbnail ${index + 1}`;
                    
                    thumbWrapper.appendChild(thumbImg);
                    modalThumbnails.appendChild(thumbWrapper);

                    thumbWrapper.addEventListener('click', () => {
                        modalMainImage.src = imgSrc;
                        document.querySelectorAll('.modal-thumbnail').forEach(t => t.classList.remove('active'));
                        thumbWrapper.classList.add('active');
                    });
                });
            }

            const specs = (card.dataset.specs || '').split('|');
            modalSpecs.innerHTML = ''; 

            if (specs.length > 0 && specs[0]) {
                specs.forEach(spec => {
                    const [key, value] = spec.split(':');
                    const li = document.createElement('li');
                    const spanKey = document.createElement('span');
                    const spanValue = document.createElement('span');
                    spanKey.textContent = key || '';
                    spanValue.textContent = value || '';
                    li.appendChild(spanKey);
                    li.appendChild(spanValue);
                    modalSpecs.appendChild(li);
                });
            }

            modal.classList.remove('hidden');
        }

        const closeModal = () => modal.classList.add('hidden');

        viewProductBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                openModal(btn.closest('.product-card'));
            });
        });

        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
        if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    }

    const sections = document.querySelectorAll('.main-section');
    const navLinks = document.querySelectorAll('.nav-links a');

    if (sections.length > 0 && navLinks.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const sectionId = entry.target.id;
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, { threshold: 0.4 });
        sections.forEach(section => observer.observe(section));
    }

    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    if (animatedElements.length > 0) {
        const animationObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });
        animatedElements.forEach(el => animationObserver.observe(el));
    }
});

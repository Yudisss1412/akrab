document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('product-modal');
    if (modal) {
        const viewProductBtns = document.querySelectorAll('.view-product-btn');
        const modalOverlay = document.querySelector('.modal-overlay');
        const closeModalBtn = document.getElementById('modal-close-btn');
        const modalImage = document.getElementById('modal-image');
        const modalStore = document.getElementById('modal-store');
        const modalTitle = document.getElementById('modal-title');
        const modalPrice = document.getElementById('modal-price');
        const modalDescription = document.getElementById('modal-description');

        const openModal = (card) => {
            if (!card) return;
            modalImage.src = card.dataset.imgSrc || 'src/product_1.png';
            modalStore.textContent = card.dataset.store || 'Nama Toko';
            modalTitle.textContent = card.dataset.title || 'Nama Produk';
            modalPrice.textContent = card.dataset.price || 'Harga tidak tersedia';
            modalDescription.textContent = card.dataset.desc || 'Deskripsi tidak tersedia.';
            modal.classList.remove('hidden');
        }

        const closeModal = () => {
            modal.classList.add('hidden');
        }

        viewProductBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const card = btn.closest('.product-card');
                openModal(card);
            });
        });

        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
        if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    }
    const sections = document.querySelectorAll('.main-section');
    const navLinks = document.querySelectorAll('.nav-links a');

    if (sections.length > 0 && navLinks.length > 0) {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.4
        };

        const observerCallback = (entries) => {
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
        };

        const observer = new IntersectionObserver(observerCallback, observerOptions);
        sections.forEach(section => observer.observe(section));
    }

    const animatedElements = document.querySelectorAll('.animate-on-scroll');

    if (animatedElements.length > 0) {
        const animationObserverOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.4
        };
        const animationObserverCallback = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        };
        const animationObserver = new IntersectionObserver(animationObserverCallback, animationObserverOptions);
        animatedElements.forEach(el => {
            animationObserver.observe(el);
        });
    }
});

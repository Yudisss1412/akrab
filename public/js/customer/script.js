// Script utama untuk halaman customer
// File ini dibuat untuk menghindari error 404

console.log('Customer main script loaded');

// Fungsi utilitas dasar jika dibutuhkan
document.addEventListener('DOMContentLoaded', function() {
    console.log('Customer DOM loaded');
    
    // Cari elemen search jika ada
    const searchInput = document.getElementById('navbar-search');
    
    if (searchInput) {
        // Tambahkan event listener untuk menangani pencarian
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Mencegah submit form default
                performSearch(searchInput.value.trim());
            }
        });
        
        // Tambahkan event listener untuk tombol pencarian jika ada
        // Mencari elemen tombol pencarian
        const searchButtons = document.querySelectorAll('.search-icon');
        searchButtons.forEach(button => {
            button.addEventListener('click', function() {
                const searchValue = searchInput.value.trim();
                if (searchValue) {
                    performSearch(searchValue);
                } else {
                    searchInput.focus();
                }
            });
        });
    }
    
    // Tambahkan event listener untuk elemen-elemen umum jika diperlukan
    // Misalnya, untuk menu hamburger, dropdown, dll.
});

// Fungsi untuk melakukan pencarian
function performSearch(query) {
    if (!query) {
        alert('Silakan masukkan kata kunci pencarian terlebih dahulu.');
        return;
    }
    
    // Redirect ke halaman hasil pencarian
    window.location.href = `/produk/search?q=${encodeURIComponent(query)}`;
}
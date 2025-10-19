document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.getElementById('reviewForm');
    
    if (reviewForm) {
        reviewForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(reviewForm);
            const submitButton = reviewForm.querySelector('button[type="submit"]');
            
            // Disable submit button while processing
            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';
            
            try {
                const response = await fetch(reviewForm.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    window.location.href = '/profil'; // Redirect to user profile
                } else {
                    alert(result.message || 'Gagal mengirim ulasan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim ulasan');
            } finally {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Kirim Ulasan';
            }
        });
    }
    
    // Initialize stars functionality - Update to work with the actual star rating implementation
    const stars = document.querySelectorAll('.rating-stars input[type="radio"]');
    stars.forEach(star => {
        star.addEventListener('change', function() {
            // When a radio button is selected, update the visual appearance
            updateStarsVisual();
        });
    });
    
    // Also handle click on the star labels
    const starLabels = document.querySelectorAll('.rating-stars label');
    starLabels.forEach(label => {
        label.addEventListener('click', function() {
            const radioId = this.getAttribute('for');
            const radio = document.getElementById(radioId);
            if (radio) {
                radio.checked = true;
                updateStarsVisual();
            }
        });
    });
    
    // Initialize visual state on load
    updateStarsVisual();
    
    function updateStarsVisual() {
        const checkedStar = document.querySelector('.rating-stars input[type="radio"]:checked');
        if (checkedStar) {
            const checkedValue = parseInt(checkedStar.value);
            const allLabels = document.querySelectorAll('.rating-stars label');
            
            allLabels.forEach((label, index) => {
                const starValue = 5 - index; // Stars are ordered in reverse (5 to 1)
                if (starValue <= checkedValue) {
                    label.style.color = '#ffc107';
                } else {
                    label.style.color = '#ddd';
                }
            });
        }
    }
});
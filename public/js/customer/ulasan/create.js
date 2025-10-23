document.addEventListener('DOMContentLoaded', function() {
    // Media upload functionality
    const mediaInput = document.getElementById('mediaInput');
    const uploadArea = document.getElementById('uploadArea');
    const mediaPreview = document.getElementById('mediaPreview');
    const browseMediaBtn = document.getElementById('browseMediaBtn');
    
    // Handle click on upload area to trigger file input
    uploadArea.addEventListener('click', function(e) {
        // Only trigger file input if not clicking on the browse button
        if (!e.target.closest('#browseMediaBtn')) {
            mediaInput.click();
        }
    });
    
    // Handle click on browse button
    browseMediaBtn.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent the event from bubbling up to uploadArea
        mediaInput.click();
    });
    
    // Handle file selection
    mediaInput.addEventListener('change', function() {
        handleMediaFiles(this.files);
    });
    
    // Handle drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });
    
    uploadArea.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        handleMediaFiles(e.dataTransfer.files);
    });
    
    function handleMediaFiles(files) {
        const validFiles = [];
        
        // Validate each file
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Check if file is an image
            if (file.type.startsWith('image/')) {
                // Check file size (max 5MB)
                if (file.size <= 5 * 1024 * 1024) {
                    validFiles.push(file);
                } else {
                    alert(`File ${file.name} terlalu besar. Maksimal 5MB per foto.`);
                }
            } else {
                alert(`File ${file.name} bukan gambar. Hanya gambar yang diperbolehkan.`);
            }
        }
        
        // Limit to 5 files total
        const currentPreviewItems = mediaPreview.querySelectorAll('.preview-item');
        if (currentPreviewItems.length + validFiles.length > 5) {
            alert('Maksimal 5 foto per ulasan.');
            return;
        }
        
        // Process valid files
        for (let i = 0; i < validFiles.length; i++) {
            const file = validFiles[i];
            const previewId = 'preview_' + Date.now() + '_' + i;
            
            // Create preview element
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.id = previewId;
            
            // Create image preview
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = 'Preview';
            
            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-media-btn';
            removeBtn.innerHTML = '&times;';
            removeBtn.title = 'Hapus foto';
            
            // Add event to remove button
            removeBtn.addEventListener('click', function() {
                document.getElementById(previewId).remove();
            });
            
            // Add elements to preview item
            previewItem.appendChild(img);
            previewItem.appendChild(removeBtn);
            
            // Add preview item to container
            mediaPreview.appendChild(previewItem);
        }
    }
    
    // Store files that have been added to the preview
    let selectedFiles = [];
    
    // Update form submission to handle media
    const reviewForm = document.getElementById('reviewForm');
    
    if (reviewForm) {
        reviewForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(reviewForm);
            
            // Add the stored files to the form data
            for (let i = 0; i < selectedFiles.length; i++) {
                formData.append('media[]', selectedFiles[i]);
            }
            
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
    
    // Update the file handling function to store files
    function handleMediaFiles(files) {
        const validFiles = [];
        
        // Validate each file
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Check if file is an image
            if (file.type.startsWith('image/')) {
                // Check file size (max 5MB)
                if (file.size <= 5 * 1024 * 1024) {
                    validFiles.push(file);
                } else {
                    alert(`File ${file.name} terlalu besar. Maksimal 5MB per foto.`);
                }
            } else {
                alert(`File ${file.name} bukan gambar. Hanya gambar yang diperbolehkan.`);
            }
        }
        
        // Limit to 5 files total
        const currentPreviewItems = mediaPreview.querySelectorAll('.preview-item');
        if (currentPreviewItems.length + validFiles.length > 5) {
            alert('Maksimal 5 foto per ulasan.');
            return;
        }
        
        // Process valid files
        for (let i = 0; i < validFiles.length; i++) {
            const file = validFiles[i];
            const previewId = 'preview_' + Date.now() + '_' + i;
            
            // Store the file
            selectedFiles.push(file);
            
            // Create preview element
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.id = previewId;
            
            // Create image preview
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = 'Preview';
            
            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-media-btn';
            removeBtn.innerHTML = '&times;';
            removeBtn.title = 'Hapus foto';
            
            // Add event to remove button
            removeBtn.addEventListener('click', function() {
                // Remove from preview
                document.getElementById(previewId).remove();
                
                // Remove from stored files
                const previewIndex = Array.from(mediaPreview.children).indexOf(document.getElementById(previewId));
                if (previewIndex !== -1) {
                    selectedFiles.splice(previewIndex, 1);
                }
            });
            
            // Add elements to preview item
            previewItem.appendChild(img);
            previewItem.appendChild(removeBtn);
            
            // Add preview item to container
            mediaPreview.appendChild(previewItem);
        }
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
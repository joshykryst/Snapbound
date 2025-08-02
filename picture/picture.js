// Global variables
let stream;
let isRecording = false;
let isPaused = false;
let isCapturing = false;
let selectedTimer = 0;
const MAX_PHOTOS = 10;
let shotsRemaining = MAX_PHOTOS;

// Add these variables at the top
let currentFacingMode = 'user';

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // AUTOMATIC RESET: Clear all photos on page load/refresh
    // Comment out this line if you're testing:
    // localStorage.removeItem('capturedImages');
    
    // Instead only reset if user explicitly asks:
    const resetRequested = localStorage.getItem('resetRequested');
    if (resetRequested === 'true') {
        localStorage.removeItem('capturedImages');
        localStorage.removeItem('resetRequested');
        shotsRemaining = MAX_PHOTOS;
    }
    
    // Get DOM elements
    const video = document.getElementById('webcam');
    const captureBtn = document.getElementById('captureBtn');
    const countdown = document.getElementById('countdown');
    const gallery = document.getElementById('gallery');
    const shotsCounter = document.getElementById('shotsCounter');
    const createStripBtn = document.getElementById('createStrip');
    const galleryHint = document.querySelector('.gallery-hint');
    const photoModal = document.getElementById('photoModal');
    const modalImage = document.getElementById('modalImage');
    const closeModal = document.querySelector('.close-modal');
    const downloadBtn = document.getElementById('downloadBtn');
    const shareBtn = document.getElementById('shareBtn');
    
    // Initialize webcam
    initializeWebcam(); 
    
    // Update shots counter
    updateShotsCounter();
    
    // Ensure gallery starts empty
    const emptyState = document.querySelector('.gallery-empty-state');
    if (emptyState) {
        emptyState.style.display = 'flex';
    }
    
    // Setup event listeners
    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            if (isCapturing) return;
            startCapture();
        });
    }
    
    // Timer buttons
    const timerBtns = document.querySelectorAll('.timer-btn');
    if (timerBtns.length) {
        timerBtns.forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.timer-btn').forEach(btn => 
                    btn.classList.remove('active'));
                button.classList.add('active');
                selectedTimer = parseInt(button.dataset.seconds);
            });
        });
    }
    
    // Update the Create PhotoStrip button handler
    if (createStripBtn) {
        createStripBtn.addEventListener('click', function() {
            const capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
            
            if (capturedImages.length < 4) {
                alert('Please capture at least 4 photos to create a photo strip.');
                return;
            }
            
            // Redirect to the new path structure
            window.location.href = 'selectphotos';
        });
    }
    
    // Modal events
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            photoModal.style.display = 'none';
        });
    }
    
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            if (modalImage.src) {
                const link = document.createElement('a');
                link.href = modalImage.src;
                link.download = 'snapbound-photo-' + Date.now() + '.jpg';
                link.click();
            }
        });
    }
    
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (navigator.share && modalImage.src) {
                fetch(modalImage.src)
                    .then(res => res.blob())
                    .then(blob => {
                        const file = new File([blob], 'snapbound-photo.jpg', { type: 'image/jpeg' });
                        navigator.share({
                            title: 'My SnapBound Photo',
                            text: 'Check out this photo I took with SnapBound!',
                            files: [file]
                        }).catch(err => {
                            console.error('Error sharing:', err);
                        });
                    });
            } else {
                alert('Sharing is not supported on your device or browser');
            }
        });
    }
    
    // Click outside modal to close
    window.addEventListener('click', function(event) {
        if (event.target === photoModal) {
            photoModal.style.display = 'none';
        }
    });
    
    // Load previous photos
    loadPreviousPhotos();
    
    // Reset button functionality
    const resetBtn = document.getElementById('resetBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete all photos? This action cannot be undone.')) {
                // Clear localStorage
                localStorage.removeItem('capturedImages');
                
                // Clear gallery
                const gallery = document.getElementById('gallery');
                if (gallery) {
                    // Remove all gallery items but keep the header
                    const galleryItems = gallery.querySelectorAll('.gallery-item');
                    galleryItems.forEach(item => item.remove());
                    
                    // Show empty state again
                    const emptyState = document.querySelector('.gallery-empty-state');
                    if (emptyState) {
                        emptyState.style.display = 'flex';
                    }
                }
                
                // Reset shots counter
                shotsRemaining = MAX_PHOTOS;
                updateShotsCounter();
                
                // Show success message
                showNotification('All photos have been deleted');
            }
        });
    }
    
    // Adjust camera section height for better viewing
    function adjustCameraHeight() {
        const cameraSection = document.querySelector('.camera-section');
        const viewportHeight = window.innerHeight;
        const topOffset = cameraSection.getBoundingClientRect().top;
        const buttonHeight = 80; // Approximate height of the buttons
        const padding = 40; // Additional padding
        
        const maxHeight = viewportHeight - topOffset - buttonHeight - padding;
        cameraSection.style.maxHeight = maxHeight + 'px';
    }
    
    // Call the function on load and resize
    window.addEventListener('load', adjustCameraHeight);
    window.addEventListener('resize', adjustCameraHeight);
    
    // Initialize button state
    updateCreateStripButton();
    
    // Camera switch button
    const switchCameraBtn = document.getElementById('switchCamera');
    if (switchCameraBtn) {
        switchCameraBtn.addEventListener('click', async function() {
            // Toggle facing mode
            currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
            
            // Stop current stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            // Reinitialize with new facing mode
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: currentFacingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                });
                
                const video = document.getElementById('webcam');
                if (video) {
                    video.srcObject = stream;
                    
                    // Wait for video to be loaded
                    video.onloadedmetadata = function() {
                        // Adjust container to match video aspect ratio
                        const cameraSection = document.querySelector('.camera-section');
                        if (cameraSection) {
                            const videoAspect = video.videoWidth / video.videoHeight;
                            cameraSection.style.aspectRatio = videoAspect;
                        }
                        
                        video.play();
                    };
                }
            } catch (error) {
                console.error('Error switching camera:', error);
                alert('Unable to switch camera. Your device might only have one camera.');
                currentFacingMode = 'user'; // Reset to front camera
            }
        });
    }
});

// Initialize webcam
async function initializeWebcam() {
    try {
        const constraints = {
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: "user",
                // Don't force a specific aspect ratio
                aspectRatio: { ideal: 4/3 }
            }
        };
        
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        const video = document.getElementById('webcam');
        
        if (video) {
            video.srcObject = stream;
            
            // Wait for video to be loaded
            video.onloadedmetadata = function() {
                // Adjust container to match video aspect ratio after loading
                const cameraSection = document.querySelector('.camera-section');
                if (cameraSection) {
                    const videoAspect = video.videoWidth / video.videoHeight;
                    cameraSection.style.aspectRatio = videoAspect;
                }
                
                video.play();
            };
            
            isRecording = true;
            
            // Remove empty state after webcam is initialized
            setTimeout(() => {
                const emptyState = document.querySelector('.gallery-empty-state');
                if (emptyState && gallery.children.length > 1) {
                    emptyState.style.display = 'none';
                }
            }, 1000);
        }
    } catch (error) {
        console.error('Error accessing webcam:', error);
        alert('Error accessing webcam. Please ensure camera permissions are granted.');
    }
}

// Take photo function
function startCapture() {
    if (selectedTimer > 0) {
        startCountdown(selectedTimer);
    } else {
        capturePhoto();
    }
}

// Countdown function
async function startCountdown(seconds) {
    const countdown = document.getElementById('countdown');
    isCapturing = true;
    
    for (let i = seconds; i > 0; i--) {
        if (countdown) {
            countdown.textContent = i;
            countdown.style.display = 'block';
        }
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
    
    if (countdown) {
        countdown.style.display = 'none';
    }
    
    capturePhoto();
}

// Capture photo function
function capturePhoto() {
    // Prevent multiple captures
    if (shotsRemaining <= 0) return;
    
    try {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const video = document.getElementById('webcam');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        let capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
        capturedImages.push(imageData);
        localStorage.setItem('capturedImages', JSON.stringify(capturedImages));

        displayPhotoInGallery(imageData);
        
        // Update counter
        shotsRemaining--;
        updateShotsCounter();
        updateCreateStripButton();  // Add this line
        
        // Play camera sound
        playShutterSound();
        
    } catch (error) {
        console.error('Error capturing photo:', error);
    } finally {
        // Reset capturing flag after delay
        setTimeout(() => {
            isCapturing = false;
        }, 1000);
    }
}

// Update the Create PhotoStrip button state
function updateCreateStripButton() {
    const capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    const createStripBtn = document.getElementById('createStrip');
    const requirement = document.querySelector('.photo-requirement');
    
    if (!createStripBtn) return;
    
    // Update the photo requirement badge
    if (requirement) {
        if (capturedImages.length >= 4) {
            requirement.style.display = 'none';
        } else {
            requirement.style.display = 'flex';
            requirement.textContent = 4 - capturedImages.length;
        }
    }
    
    // Visual indication if enough photos
    if (capturedImages.length >= 4) {
        createStripBtn.classList.remove('disabled');
        createStripBtn.style.background = 'var(--dark-color)';
    } else {
        createStripBtn.classList.add('disabled');
        createStripBtn.style.background = '#999';
    }
}

// Update shots counter display
function updateShotsCounter() {
    const shotsCounter = document.getElementById('shotsCounter');
    if (shotsCounter) {
        shotsCounter.querySelector('span').textContent = `${shotsRemaining} shots left`;
    }
}

// Display photo in gallery
function displayPhotoInGallery(imageData) {
    const gallery = document.getElementById('gallery');
    if (!gallery) return;
    
    // Remove empty state if exists
    const emptyState = document.querySelector('.gallery-empty-state');
    if (emptyState) {
        emptyState.style.display = 'none';
    }
    
    // Create new gallery item
    const container = document.createElement('div');
    container.className = 'gallery-item';
    
    // Create image
    const img = document.createElement('img');
    img.src = imageData;
    img.alt = 'Captured photo';
    
    // Add click event to open modal
    container.addEventListener('click', function() {
        const modal = document.getElementById('photoModal');
        const modalImage = document.getElementById('modalImage');
        
        if (modal && modalImage) {
            modalImage.src = imageData;
            modal.style.display = 'flex';
        }
    });
    
    // Add to gallery
    container.appendChild(img);
    gallery.insertBefore(container, gallery.querySelector('.gallery-header').nextSibling);

    // Show gallery hint after first photo
    const galleryHint = document.querySelector('.gallery-hint');
    if (galleryHint && gallery.querySelectorAll('.gallery-item').length > 2) {
        galleryHint.classList.add('show');
        // Hide hint after 3 seconds
        setTimeout(() => {
            galleryHint.classList.remove('show');
        }, 3000);
    }
    
    // If more than 3 photos, scroll to top to show latest
    if (gallery.querySelectorAll('.gallery-item').length > 3) {
        gallery.scrollTop = 0;
    }
}

// Load previous photos from localStorage
function loadPreviousPhotos() {
    const capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    const gallery = document.getElementById('gallery');
    
    if (capturedImages.length > 0 && gallery) {
        // Remove empty state
        const emptyState = document.querySelector('.gallery-empty-state');
        if (emptyState) {
            emptyState.style.display = 'none';
        }
        
        // Add photos to gallery (in reverse order - newest first)
        capturedImages.slice().reverse().forEach(imageData => {
            const container = document.createElement('div');
            container.className = 'gallery-item';
            
            const img = document.createElement('img');
            img.src = imageData;
            img.alt = 'Captured photo';
            
            // Add click event
            container.addEventListener('click', function() {
                const modal = document.getElementById('photoModal');
                const modalImage = document.getElementById('modalImage');
                
                if (modal && modalImage) {
                    modalImage.src = imageData;
                    modal.style.display = 'flex';
                }
            });
            
            container.appendChild(img);
            gallery.insertBefore(container, gallery.querySelector('.gallery-header').nextSibling);
        });
        
        // Update shots counter
        shotsRemaining = Math.max(0, MAX_PHOTOS - capturedImages.length);
        updateShotsCounter();
        updateCreateStripButton();  // Add this line
    }
}

// Play camera shutter sound
function playShutterSound() {
    const audio = new Audio('https://www.soundjay.com/mechanical/sounds/camera-shutter-click-01.mp3');
    audio.play().catch(e => console.log('Audio play failed:', e));
}

// Add this notification function outside any other function
function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    
    // Add to body
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Hide and remove notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
// Global variables
let stream;
let isRecording = false;
let isPaused = false;
let isCapturing = false;
let selectedTimer = 0;
const MAX_PHOTOS = 10;
let shotsRemaining = MAX_PHOTOS;

// DOM Elements
let video;
let canvas;
let captureBtn;
let countdown;
let gallery;
let shotsCounter;
let pauseBtn;
let stopBtn;

// Move all DOM element selections inside the DOMContentLoaded event
document.addEventListener('DOMContentLoaded', () => {
    // Get DOM elements
    video = document.getElementById('webcam');
    captureBtn = document.getElementById('captureBtn');
    countdown = document.getElementById('countdown');
    gallery = document.getElementById('gallery');
    shotsCounter = document.getElementById('shotsCounter');
    pauseBtn = document.getElementById('pauseBtn');
    stopBtn = document.getElementById('stopBtn');

    // Initialize webcam
    initializeWebcam();

    // Timer button functionality
    document.querySelectorAll('.timer-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.timer-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedTimer = parseInt(btn.dataset.seconds);
        });
    });

    // Capture button functionality
    if (captureBtn) {
        captureBtn.addEventListener('click', async () => {
            if (isCapturing || shotsRemaining <= 0) return;
            
            if (selectedTimer > 0) {
                await startCountdown(selectedTimer);
            }
            capturePhoto();
        });
    }

    // Safe event listener setup for pauseBtn
    if (pauseBtn) {
        pauseBtn.addEventListener('click', togglePause);
    } else {
        console.warn('Element with ID "pauseBtn" not found');
    }

    // Safe event listener setup for stopBtn
    if (stopBtn) {
        stopBtn.addEventListener('click', stopCapture);
    } else {
        console.warn('Element with ID "stopBtn" not found');
    }
});

// Initialize webcam
async function initializeWebcam() {
    try {
        // First try to get the viewport dimensions
        const isPortrait = window.innerHeight > window.innerWidth;
        
        const constraints = {
            video: {
                width: isPortrait ? { ideal: 720 } : { ideal: 1280 },
                height: isPortrait ? { ideal: 1280 } : { ideal: 720 },
                // Remove fixed aspect ratio to allow proper orientation
                facingMode: 'user'
            },
            audio: false
        };

        stream = await navigator.mediaDevices.getUserMedia(constraints);
        
        if (video) {
            video.srcObject = stream;
            video.style.transform = 'none';
            
            // Add event listener for video metadata loaded
            video.addEventListener('loadedmetadata', () => {
                // Set video element dimensions based on actual stream
                const streamWidth = video.videoWidth;
                const streamHeight = video.videoHeight;
                
                // Calculate aspect ratio
                const aspectRatio = streamWidth / streamHeight;
                
                if (isPortrait) {
                    // For portrait mode
                    video.style.width = '100%';
                    video.style.height = 'auto';
                    video.style.maxHeight = '80vh';
                } else {
                    // For landscape mode
                    video.style.width = '100%';
                    video.style.height = 'auto';
                }
                
                // Update camera section container
                const cameraSection = document.querySelector('.camera-section');
                if (cameraSection) {
                    cameraSection.style.aspectRatio = `${streamWidth}/${streamHeight}`;
                }
            });
            
            await video.play();
            isRecording = true;
        }
    } catch (err) {
        console.error('Error accessing webcam:', err);
        isRecording = false;
    }
}

// Take photo function
function takePhoto() {
    if (!isRecording || remainingShots <= 0) return;

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw without flipping
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert to base64 and save to localStorage
    const imageData = canvas.toDataURL('image/jpeg', 0.8);
    let capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    capturedImages.push(imageData);
    localStorage.setItem('capturedImages', JSON.stringify(capturedImages));

    // Display in gallery
    displayPhotoInGallery(imageData);
    
    // Update shots counter
    remainingShots--;
    shotsCounter.textContent = `Shots remaining: ${remainingShots}`;

    // Redirect when all photos are taken
    if (remainingShots === 0) {
        setTimeout(() => {
            window.location.href = 'selectphotos.html';
        }, 1000);
    }
}

// Display photo in gallery
function displayPhotoInGallery(imageData) {
    if (!gallery) return;
    
    const img = document.createElement('img');
    img.src = imageData;
    img.alt = 'Captured photo';
    img.style.objectFit = 'cover';
    img.style.width = '100%';
    img.style.height = '100%';
    
    const container = document.createElement('div');
    container.className = 'gallery-item';
    container.appendChild(img);
    
    gallery.insertBefore(container, gallery.firstChild);

    // If more than 3 photos, scroll to top to show latest
    if (gallery.children.length > 3) {
        gallery.scrollTop = 0;
    }

    // Show gallery hint after first photo
    const galleryHint = document.querySelector('.gallery-hint');
    if (galleryHint) {
        galleryHint.style.opacity = '1';
        // Hide hint after 3 seconds
        setTimeout(() => {
            galleryHint.style.opacity = '0';
            galleryHint.style.transition = 'opacity 0.5s ease';
        }, 3000);
    }
}

// Update the capture photo function
function capturePhoto() {
    // Prevent multiple captures
    if (isCapturing || shotsRemaining <= 0) return;
    isCapturing = true;

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
    } catch (error) {
        console.error('Error capturing photo:', error);
    } finally {
        // Reset capturing flag after delay
        setTimeout(() => {
            isCapturing = false;
        }, 1000);
    }
}

// Update shots counter display
function updateShotsCounter() {
    const shotsCounter = document.getElementById('shotsCounter');
    if (shotsCounter) {
        shotsCounter.textContent = `${shotsRemaining} shots left`;
    }
}

// Handle timer and capture
function startCapture() {
    if (selectedTimer > 0) {
        let timeLeft = selectedTimer;
        countdown.style.display = 'block';
        
        const timer = setInterval(() => {
            countdown.textContent = timeLeft;
            timeLeft--;
            
            if (timeLeft < 0) {
                clearInterval(timer);
                countdown.style.display = 'none';
                takePhoto();
            }
        }, 1000);
    } else {
        takePhoto();
    }
}

// Pause and Resume function
function togglePause() {
    if (!isRecording) return;
    
    const pauseBtn = document.getElementById('pauseBtn');
    
    if (isPaused) {
        // Resume video
        video.play();
        isPaused = false;
        pauseBtn.textContent = 'Pause';
        captureBtn.disabled = false;
    } else {
        // Pause video
        video.pause();
        isPaused = true;
        pauseBtn.textContent = 'Resume';
        captureBtn.disabled = true;
    }
}

// Stop capture function
function stopCapture() {
    if (!isRecording) return;
    
    // Stop all tracks from the stream
    stream.getTracks().forEach(track => track.stop());
    
    // Reset video
    video.srcObject = null;
    isRecording = false;
    
    // Hide video controls
    pauseBtn.style.display = 'none';
    stopBtn.style.display = 'none';
    
    // Disable capture button
    captureBtn.disabled = true;
    
    // Redirect to select photos page if photos were taken
    const storedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    if (storedImages.length > 0) {
        window.location.href = 'selectphotos.html';
    }
}

// Countdown function
async function startCountdown(seconds) {
    isCapturing = true;
    for (let i = seconds; i > 0; i--) {
        if (countdown) {  // Changed from countdownElement to countdown
            countdown.textContent = i;
            countdown.style.display = 'block';
        }
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
    if (countdown) {  // Changed from countdownElement to countdown
        countdown.style.display = 'none';
    }
    isCapturing = false;
}

// Update the gallery display function
function displayPhotoInGallery(imageData) {
    const gallery = document.getElementById('gallery');
    if (!gallery) return;
    
    const container = document.createElement('div');
    container.className = 'gallery-item';
    
    const img = document.createElement('img');
    img.src = imageData;
    img.alt = 'Captured photo';
    img.style.objectFit = 'cover';
    img.style.width = '100%';
    img.style.height = '100%';
    
    container.appendChild(img);
    gallery.insertBefore(container, gallery.firstChild);

    // Show gallery hint after first photo
    const galleryHint = document.querySelector('.gallery-hint');
    if (galleryHint) {
        galleryHint.style.opacity = '1';
        // Hide hint after 3 seconds
        setTimeout(() => {
            galleryHint.style.opacity = '0';
            galleryHint.style.transition = 'opacity 0.5s ease';
        }, 3000);
    }
}

// Add this new function to handle navigation
window.proceedToPhotostrip = function() {
    const capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    if (capturedImages.length === 0) {
        alert('Please take at least one photo before creating a photostrip');
        return;
    }
    window.location.href = 'selectphotos.html';
}

// Add event listener for create strip button
const createStripBtn = document.getElementById('createStrip');
if (createStripBtn) {
    createStripBtn.addEventListener('click', () => {
        const capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
        if (capturedImages.length === 0) {
            alert('Please take at least one photo before creating a photostrip');
            return;
        }
        window.location.href = 'selectphotos.html';
    });
}
// Global variables
let stream;
let isRecording = false;
let remainingShots = 10;
let selectedTimer = 0;
let isPaused = false;

// DOM Elements
const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('captureBtn');
const countdown = document.getElementById('countdown');
const gallery = document.getElementById('gallery');
const shotsCounter = document.getElementById('shotsCounter');

// Add this constant at the top of your file
const MAX_PHOTOS = 10;
let shotsRemaining = MAX_PHOTOS;

// Initialize webcam
async function initializeWebcam() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: 1280,
                height: 720,
                facingMode: 'user'
            }, 
            audio: false 
        });
        
        if (video) {
            video.srcObject = stream;
            video.style.transform = 'scaleX(-1)'; // Mirror effect
            await video.play();
            
            // Show control buttons after successful initialization
            document.getElementById('pauseBtn').style.display = 'block';
            document.getElementById('stopBtn').style.display = 'block';
            
            isRecording = true;
        }
    } catch (err) {
        console.error('Error accessing webcam:', err);
        alert('Please allow camera access to use this application');
    }
}

// Take photo function
function takePhoto() {
    if (!isRecording || remainingShots <= 0) return;

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Flip the image horizontally to match the preview
    context.scale(-1, 1);
    context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
    
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
    
    const container = document.createElement('div');
    container.className = 'gallery-item';
    container.appendChild(img);
    
    gallery.insertBefore(container, gallery.firstChild);

    // If more than 3 photos, scroll to top to show latest
    if (gallery.children.length > 3) {
        gallery.scrollTop = 0;
    }
}

// Update your capture function
function capturePhoto() {
    if (shotsRemaining <= 0) {
        alert('Maximum photos reached! Redirecting to photo strip creation...');
        window.location.href = 'selectphotos.html';
        return;
    }

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const video = document.getElementById('webcam');
    
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.scale(-1, 1); // Mirror effect
    context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
    
    // Convert to base64 and save to localStorage
    const imageData = canvas.toDataURL('image/jpeg', 0.8);
    let capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    capturedImages.push(imageData);
    localStorage.setItem('capturedImages', JSON.stringify(capturedImages));

    // Display in gallery
    displayPhotoInGallery(imageData);
    
    // Update shots counter
    shotsRemaining--;
    const shotsCounter = document.getElementById('shotsCounter');
    if (shotsCounter) {
        shotsCounter.textContent = `Shots remaining: ${shotsRemaining}`;
    }

    // Check if we've reached the limit
    if (shotsRemaining === 0) {
        setTimeout(() => {
            alert('Maximum photos reached! Redirecting to photo strip creation...');
            window.location.href = 'selectphotos.html';
        }, 500); // Small delay to show the last photo
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

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Initialize variables
    const MAX_PHOTOS = 10;
    let shotsRemaining = MAX_PHOTOS;
    let isCapturing = false;

    // Get DOM elements
    const captureBtn = document.getElementById('captureBtn');
    const shotsCounter = document.getElementById('shotsCounter');
    const video = document.getElementById('webcam');

    // Update the capture photo function
    function capturePhoto() {
        if (isCapturing || shotsRemaining <= 0) return;
        isCapturing = true;

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.scale(-1, 1);
        context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
        
        // Save photo to localStorage
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        let capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
        capturedImages.push(imageData);
        localStorage.setItem('capturedImages', JSON.stringify(capturedImages));

        // Display in gallery
        displayPhotoInGallery(imageData);
        
        // Update counter
        shotsRemaining--;
        shotsCounter.textContent = `Shots remaining: ${shotsRemaining}`;

        // Check for redirection
        if (shotsRemaining === 0) {
            setTimeout(() => {
                alert('Maximum photos reached! Redirecting to photo strip creation...');
                window.location.replace('selectphotos.html');
            }, 500);
        }

        isCapturing = false;
    }

    // Add click event listener to capture button
    captureBtn.addEventListener('click', capturePhoto);

    // Initialize webcam
    async function initializeWebcam() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: 1280,
                    height: 720,
                    facingMode: 'user'
                }, 
                audio: false 
            });
            
            if (video) {
                video.srcObject = stream;
                video.style.transform = 'scaleX(-1)'; // Mirror effect
                await video.play();
                
                // Show control buttons after successful initialization
                document.getElementById('pauseBtn').style.display = 'block';
                document.getElementById('stopBtn').style.display = 'block';
                
                isRecording = true;
            }
        } catch (err) {
            console.error('Error accessing webcam:', err);
            alert('Please allow camera access to use this application');
        }
    }

    // Initialize the application
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

    // Countdown function
    async function startCountdown(seconds) {
        isCapturing = true;
        for (let i = seconds; i > 0; i--) {
            if (countdownElement) {
                countdownElement.textContent = i;
                countdownElement.style.display = 'block';
            }
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
        if (countdownElement) {
            countdownElement.style.display = 'none';
        }
        isCapturing = false;
    }

    // Capture photo function
    function capturePhoto() {
        if (shotsRemaining <= 0) {
            alert('Maximum photos reached! Redirecting to photo strip creation...');
            window.location.href = 'selectphotos.html';
            return;
        }

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        const video = document.getElementById('webcam');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.scale(-1, 1); // Mirror effect
        context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
        
        // Convert to base64 and save to localStorage
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        
        // Get existing images array or create new one
        let capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
        capturedImages.push(imageData);
        
        // Save updated array back to localStorage
        localStorage.setItem('capturedImages', JSON.stringify(capturedImages));
        console.log('Photo saved to localStorage, total photos:', capturedImages.length);

        // Display in gallery
        displayPhotoInGallery(imageData);
        
        // Update shots counter
        shotsRemaining--;
        if (shotsCounter) {
            shotsCounter.textContent = `Shots remaining: ${shotsRemaining}`;
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

    // Add this function to display photos in gallery
    function displayPhotoInGallery(imageData) {
        const gallery = document.getElementById('gallery');
        if (!gallery) return;
        
        const container = document.createElement('div');
        container.className = 'gallery-item';
        
        const img = document.createElement('img');
        img.src = imageData;
        img.alt = 'Captured photo';
        
        container.appendChild(img);
        gallery.insertBefore(container, gallery.firstChild);

        // If more than 3 photos, scroll to top to show latest
        if (gallery.children.length > 3) {
            gallery.scrollTop = 0;
        }
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
});

captureBtn.addEventListener('click', startCapture);

document.querySelectorAll('.timer-btn').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.timer-btn').forEach(btn => 
            btn.classList.remove('active'));
        button.classList.add('active');
        selectedTimer = parseInt(button.dataset.seconds);
    });
});

document.getElementById('pauseBtn').addEventListener('click', togglePause);
document.getElementById('stopBtn').addEventListener('click', stopCapture);
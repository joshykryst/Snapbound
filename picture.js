const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('capture');
const gallery = document.getElementById('gallery');
const context = canvas.getContext('2d');
const countdownEl = document.getElementById('countdown');
const timerButtons = document.querySelectorAll('.timer-btn');
const maxShots = 10;
const pauseBtn = document.getElementById('pauseBtn');
const stopBtn = document.getElementById('stopBtn');
const shotsCounter = document.getElementById('shotsCounter');

let isPaused = false;
let stream = null;
let selectedTimer = 0;
let isCountingDown = false;
let currentShots = 0;
let isProcessing = false;
const DISPLAY_TIME = 3000;
let isAutoShooting = false;
let countInterval = null;
let remainingTime = 0;

// Initialize webcam
async function initWebcam() {
    try {
        // Check if getUserMedia is supported
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Camera API is not supported in your browser');
        }

        // Request camera permissions explicitly
        const permission = await navigator.permissions.query({ name: 'camera' });
        if (permission.state === 'denied') {
            throw new Error('Camera permission denied');
        }

        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        
        const constraints = {
            video: {
                facingMode: isMobile ? "environment" : "user",
                width: { ideal: isMobile ? 1080 : 1280 },
                height: { ideal: isMobile ? 1920 : 720 },
                aspectRatio: isMobile ? 9/16 : 16/9
            }
        };

        if (isMobile && /iPhone|iPad|iPod/i.test(navigator.userAgent)) {
            constraints.video.facingMode = { exact: "environment" };
        }

        stream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = stream;
        
        video.setAttribute('playsinline', true);
        video.setAttribute('autoplay', true);
        await video.play();

        video.onloadedmetadata = () => {
            captureButton.disabled = false;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
        };

        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        
        if (videoDevices.length > 1 && isMobile) {
            createCameraSwitchButton();
        }

        updateShotsCounter();
        captureButton.style.display = 'block';
        pauseBtn.style.display = 'none';
        stopBtn.style.display = 'none';
    } catch (err) {
        console.error("Error accessing camera:", err);
        alert("Error accessing camera. Please make sure you've granted camera permissions.");
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    captureButton.disabled = false;
    initWebcam();
});

captureButton.addEventListener('click', async () => {
    if (!isProcessing && stream) {
        if (currentShots >= maxShots) {
            alert('Maximum number of shots reached!');
            return;
        }

        isProcessing = true;
        if (selectedTimer > 0) {
            await startCountdown(selectedTimer);
        }
        
        // Take the picture
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = canvas.toDataURL('image/jpeg');
        
        // Save to localStorage
        const photos = JSON.parse(localStorage.getItem('capturedPhotos') || '[]');
        photos.push(imageData);
        localStorage.setItem('capturedPhotos', JSON.stringify(photos));
        
        // Add to gallery
        addToGallery(imageData);
        
        currentShots++;
        updateShotsCounter();
        isProcessing = false;
    }
});

// Timer button functionality
timerButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        if (!isProcessing && !isPaused) {
            timerButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedTimer = parseInt(btn.dataset.seconds);
        }
    });
});

// Initialize
initWebcam();

// Add this function to update shots counter
function updateShotsCounter() {
    const remaining = maxShots - currentShots;
    shotsCounter.textContent = `Shots remaining: ${remaining}`;
}

// Add this function to add photos to gallery
function addToGallery(imageData) {
    const img = document.createElement('img');
    img.src = imageData;
    img.className = 'captured-image';
    gallery.insertBefore(img, gallery.firstChild);
}
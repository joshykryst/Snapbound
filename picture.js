const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('capture');
const pauseBtn = document.getElementById('pauseBtn');
const stopBtn = document.getElementById('stopBtn');
const countdown = document.getElementById('countdown');
const shotsCounter = document.getElementById('shotsCounter');
const gallery = document.getElementById('gallery');

let stream = null;
let selectedTimer = 0;
let isCountingDown = false;
let currentShots = 0;
const maxShots = 10;
let isPaused = false;

// Initialize webcam
async function initWebcam() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        });
        video.srcObject = stream;
        await video.play();
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        updateShotsCounter();
        enableControls();
    } catch (err) {
        console.error("Error accessing camera:", err);
        alert("Error accessing camera. Please ensure camera permissions are granted.");
    }
}

// Timer functionality
document.querySelectorAll('.timer-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.timer-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedTimer = parseInt(btn.dataset.seconds);
    });
});

// Start countdown
async function startCountdown(seconds) {
    if (isCountingDown) return;
    isCountingDown = true;
    countdown.style.display = 'block';
    
    for (let i = seconds; i > 0; i--) {
        countdown.textContent = i;
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
    
    countdown.style.display = 'none';
    isCountingDown = false;
    return takePicture();
}

// Take picture functionality
async function takePicture() {
    if (currentShots >= maxShots) {
        alert('Maximum number of shots reached!');
        return;
    }

    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    const imageData = canvas.toDataURL('image/jpeg');
    addToGallery(imageData);
    
    currentShots++;
    updateShotsCounter();
    
    // Save to localStorage
    const photos = JSON.parse(localStorage.getItem('capturedPhotos') || '[]');
    photos.push(imageData);
    localStorage.setItem('capturedPhotos', JSON.stringify(photos));
}

// Capture button click handler
captureButton.addEventListener('click', async () => {
    if (selectedTimer > 0) {
        await startCountdown(selectedTimer);
    } else {
        await takePicture();
    }
});

// Pause functionality
pauseBtn.addEventListener('click', () => {
    if (stream) {
        if (!isPaused) {
            video.pause();
            pauseBtn.textContent = 'Resume';
        } else {
            video.play();
            pauseBtn.textContent = 'Pause';
        }
        isPaused = !isPaused;
    }
});

// Stop functionality
stopBtn.addEventListener('click', () => {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        disableControls();
    }
});

// Helper functions
function updateShotsCounter() {
    shotsCounter.textContent = `Shots remaining: ${maxShots - currentShots}`;
}

function addToGallery(imageData) {
    const img = document.createElement('img');
    img.src = imageData;
    img.className = 'captured-image';
    gallery.insertBefore(img, gallery.firstChild);
}

function enableControls() {
    captureButton.disabled = false;
    pauseBtn.disabled = false;
    stopBtn.disabled = false;
}

function disableControls() {
    captureButton.disabled = true;
    pauseBtn.disabled = true;
    stopBtn.disabled = true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initWebcam);
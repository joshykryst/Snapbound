// Global variables
let stream;
let timerValue = 0;
let remainingShots = 10;
let isRecording = false;
let isPaused = false;
let hasStartedCapturing = false;
let capturedImages = []; // Add this at the start of your file with other variables

// DOM Elements
const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('capture');
const pauseBtn = document.getElementById('pauseBtn');
const stopBtn = document.getElementById('stopBtn');
const countdown = document.getElementById('countdown');
const shotsCounter = document.getElementById('shotsCounter');
const timerButtons = document.querySelectorAll('.timer-btn');
const gallery = document.getElementById('gallery');
    
// Initialize webcam
async function initializeWebcam() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: true,
            audio: false
        });
        video.srcObject = stream;
        isRecording = true;
    } catch (err) {
        console.error('Error accessing webcam:', err);
        alert('Could not access webcam. Please check permissions.');
    }
}

// Timer buttons functionality
timerButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        timerButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        timerValue = parseInt(btn.dataset.seconds);
    });
});

// Show control buttons after first picture
function showControlButtons() {
    const controlButtons = document.querySelector('.control-buttons');
    controlButtons.style.display = 'flex';
}

// Capture functionality
async function captureImage() {
    if (remainingShots <= 0) {
        alert('No shots remaining!');
        return;
    }

    if (timerValue > 0) {
        // Start countdown
        let timeLeft = timerValue;
        countdown.style.display = 'block';
        
        const countdownInterval = setInterval(() => {
            countdown.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                countdown.style.display = 'none';
                takePhoto();
                showControlButtons(); // Show controls after first photo
            }
            timeLeft--;
        }, 1000);
    } else {
        takePhoto();
        showControlButtons(); // Show controls after first photo
    }
}

function takePhoto() {
    if (!isRecording || isPaused) return;

    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Get image data and store it
    const imageData = canvas.toDataURL('image/jpeg');
    capturedImages.push(imageData);
    
    // Convert to image and add to gallery
    const img = document.createElement('img');
    img.src = imageData;
    gallery.insertBefore(img, gallery.firstChild);
    
    // Update shots counter
    remainingShots--;
    shotsCounter.textContent = `Shots remaining: ${remainingShots}`;
}

// Pause/Resume functionality
pauseBtn.addEventListener('click', () => {
    if (!isRecording) return;
    
    isPaused = !isPaused;
    pauseBtn.textContent = isPaused ? 'Resume' : 'Pause';
    
    if (isPaused) {
        video.pause();
    } else {
        video.play();
    }
});

// Stop functionality
stopBtn.addEventListener('click', () => {
    isRecording = false;
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    video.srcObject = null;
    
    // Store captured images in localStorage
    localStorage.setItem('capturedImages', JSON.stringify(capturedImages));
    
    // Redirect to select photos page
    window.location.href = 'selectphotos.html';
});

// Event listeners
captureBtn.addEventListener('click', captureImage);
document.addEventListener('DOMContentLoaded', initializeWebcam);
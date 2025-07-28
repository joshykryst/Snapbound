// Global variables
let stream;
let timerValue = 0;
let remainingShots = 10;
let isRecording = false;
let selectedTimer = 0;
const MAX_SHOTS = 10;
let shotsTaken = 0;

// DOM Elements
const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('captureBtn');
const pauseBtn = document.getElementById('pauseBtn');
const stopBtn = document.getElementById('stopBtn');
const countdown = document.getElementById('countdown');
const gallery = document.getElementById('gallery');
const shotsCounter = document.getElementById('shotsCounter');

// Initialize webcam
async function initializeWebcam() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });
        video.srcObject = stream;
        video.play();
        isRecording = true;
        captureBtn.disabled = false;
    } catch (err) {
        console.error('Error accessing webcam:', err);
        alert('Could not access webcam. Please check permissions.');
    }
}

// Capture photo function
function capturePhoto() {
    if (selectedTimer > 0) {
        let timeLeft = selectedTimer;
        const countdown = document.getElementById('countdown');
        countdown.style.display = 'block';
        
        const timer = setInterval(() => {
            countdown.textContent = timeLeft;
            timeLeft--;
            
            if (timeLeft < 0) {
                clearInterval(timer);
                countdown.style.display = 'none';
                takeActualPhoto();
            }
        }, 1000);
    } else {
        takeActualPhoto();
    }
}

function takeActualPhoto() {
    if (!isRecording || remainingShots <= 0) return;

    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Get image data and store it
    const imageData = canvas.toDataURL('image/jpeg');
    
    // Store in localStorage
    const storedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    storedImages.push(imageData);
    localStorage.setItem('capturedImages', JSON.stringify(storedImages));
    
    // Update shots counter
    remainingShots--;
    shotsCounter.textContent = `Shots remaining: ${remainingShots}`;

    // Redirect when all photos are taken
    if (remainingShots === 0) {
        setTimeout(() => {
            window.location.href = 'selectphotos.html';
        }, 500);
    }
}

// Event listeners
captureBtn.addEventListener('click', capturePhoto);
document.addEventListener('DOMContentLoaded', initializeWebcam);

// Stop functionality
stopBtn.addEventListener('click', () => {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    window.location.href = 'selectphotos.html';
});

// Add timer button functionality
document.querySelectorAll('.timer-btn').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons
        document.querySelectorAll('.timer-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        button.classList.add('active');
        
        // Set timer value
        selectedTimer = parseInt(button.dataset.seconds);
    });
});
// Global variables
let stream;
let timerValue = 0;
let remainingShots = 10;
let isRecording = false;

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
    if (!isRecording || remainingShots <= 0) return;

    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Create image from canvas and add to gallery
    const img = document.createElement('img');
    img.src = canvas.toDataURL('image/jpeg');
    img.className = 'captured-image';
    
    // Add image to the beginning of gallery
    gallery.insertBefore(img, gallery.firstChild);
    
    // Update shots counter
    remainingShots--;
    shotsCounter.textContent = `Shots remaining: ${remainingShots}`;
    
    // Show control buttons
    pauseBtn.style.display = 'block';
    stopBtn.style.display = 'block';
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
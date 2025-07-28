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
        await video.play();
        
        // Show control buttons after successful initialization
        document.getElementById('pauseBtn').style.display = 'block';
        document.getElementById('stopBtn').style.display = 'block';
        
        isRecording = true;
    } catch (err) {
        console.error('Error accessing webcam:', err);
        alert('Could not access webcam. Please check permissions.');
    }
}

// Take photo function
function takePhoto() {
    if (!isRecording || remainingShots <= 0) return;

    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Create image container and image
    const imgContainer = document.createElement('div');
    imgContainer.className = 'captured-image-container';
    
    const img = document.createElement('img');
    img.src = canvas.toDataURL('image/jpeg');
    
    imgContainer.appendChild(img);
    
    // Add to gallery (at the beginning)
    if (gallery.firstChild) {
        gallery.insertBefore(imgContainer, gallery.firstChild);
    } else {
        gallery.appendChild(imgContainer);
    }

    // If more than 3 photos, scroll to top to show latest
    if (gallery.children.length > 3) {
        gallery.scrollTop = 0;
    }

    // Store in localStorage
    const storedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    storedImages.push(img.src);
    localStorage.setItem('capturedImages', JSON.stringify(storedImages));

    // Update counter
    remainingShots--;
    shotsCounter.textContent = `Shots remaining: ${remainingShots}`;

    // Redirect when all photos are taken
    if (remainingShots === 0) {
        setTimeout(() => {
            window.location.href = 'selectphotos.html';
        }, 1000);
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
document.addEventListener('DOMContentLoaded', initializeWebcam);

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Photos - SnapBound</title>
    <link rel="stylesheet" href="picture.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@jaames/iro@5"></script>
    <style>
        /* Page-specific styles that integrate with your design system */
        .select-photos-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 100px;
            margin-bottom: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .page-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--dark-color);
            grid-column: 1 / -1;
        }
        
        .page-title span {
            color: var(--primary-color);
        }
        
        .section-header {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            position: relative;
            color: var(--dark-color);
            grid-column: 1 / -1;
        }
        
        .section-header:after {
            content: '';
            position: absolute;
            width: 60px;
            height: 3px;
            background: var(--primary-color);
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .strip-layout {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .preview-section {
            position: sticky;
            top: 120px;
            align-self: flex-start;
        }

        .strip-preview {
            width: 180px;
            height: 600px;
            border: 3px solid transparent;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-color: var(--primary-color);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
        }

        .photo-placeholder {
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            overflow: hidden;
        }

        .photo-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .options-section {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .options-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--box-shadow);
        }

        .color-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }

        .color-preview:hover {
            transform: scale(1.1);
        }

        .color-palette {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .custom-color-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .color-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .color-modal.active {
            opacity: 1;
            pointer-events: all;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        .effect-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .effect-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            border: 2px solid #eee;
        }

        .effect-circle.active {
            border-color: var(--primary-color);
            background: rgba(255, 87, 87, 0.1);
            transform: scale(1.05);
        }

        .effect-circle i {
            font-size: 20px;
            margin-bottom: 5px;
            color: var(--primary-color);
        }

        .effect-circle span {
            font-size: 10px;
            text-align: center;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin: 30px 0;
            grid-column: 1 / -1;
        }

        .photo-container {
            position: relative;
            aspect-ratio: 3/4;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .photo-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: filter 0.3s ease;
        }

        .photo-checkbox {
            position: absolute;
            top: 10px;
            left: 10px;
            transform: scale(1.5);
            z-index: 2;
            cursor: pointer;
            background: white;
            border-radius: 3px;
            width: 20px;
            height: 20px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            outline: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-checkbox:checked {
            background-color: var(--primary-color);
            border-color: white;
        }

        .photo-checkbox:checked::after {
            content: '✓';
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .selection-controls {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
            grid-column: 1 / -1;
        }

        .selection-controls button {
            padding: 12px 30px;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(255, 87, 87, 0.3);
            font-family: inherit;
            font-weight: 500;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .selection-controls button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 87, 87, 0.4);
        }

        .selection-controls button i {
            font-size: 18px;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 10px 20px;
            background: transparent;
            color: var(--dark-color);
            border: 2px solid #ddd;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
            text-decoration: none;
        }
        
        .back-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .selected-count {
            position: sticky;
            top: 80px;
            z-index: 100;
            background: var(--gradient-primary);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: var(--box-shadow);
            margin: 0 auto 20px;
            width: fit-content;
            display: flex;
            align-items: center;
            gap: 10px;
            grid-column: 1 / -1;
        }
        
        /* Notification styles */
        .notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 2000;
            opacity: 0;
            transition: all 0.3s ease;
            text-align: center;
        }

        .notification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .debug-bar {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 9999;
            max-width: 300px;
            overflow: auto;
            max-height: 150px;
        }

        .strip-text-preview {
            margin-top: auto;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
            padding: 10px 0;
            letter-spacing: 0.5px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .select-photos-container {
                grid-template-columns: 1fr;
            }
            
            .preview-section {
                position: static;
                margin: 0 auto;
            }
            
            .selection-controls {
                flex-direction: column;
                align-items: center;
            }
            
            .selection-controls button {
                width: 100%;
                justify-content: center;
            }
        }


.template-options {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin: 20px 0;
}

.template-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.template-option:hover {
    transform: translateY(-5px);
}

.template-option.active {
    position: relative;
}

.template-option.active::after {
    content: '';
    position: absolute;
    bottom: -10px;
    width: 80%;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

.template-preview {
    width: 100px;
    height: 150px;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 8px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.default-template {
    background: linear-gradient(135deg, #FF5757 0%, #FF8A8A 100%);
}

.halloween-template {
    background: linear-gradient(135deg, #6a3093 0%, #a044ff 100%);
}

.vintage-template {
    background: linear-gradient(135deg, #8E6C4F 0%, #D7B38A 100%);
}

.preview-strip {
    display: flex;
    flex-direction: column;
    gap: 8px;
    height: 100%;
}

.preview-photo {
    flex: 1;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.halloween-decoration {
    position: absolute;
    top: 5px;
    right: 5px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.halloween-decoration i {
    font-size: 12px;
    color: #FF9800;
    text-shadow: 0 0 3px rgba(0,0,0,0.5);
}

.save-template-btn {
    width: 100%;
    padding: 12px;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    font-weight: 500;
    margin-top: 15px;
    transition: all 0.3s ease;
}

.save-template-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 87, 87, 0.3);
}
        
    </style>
</head>
<body class="booth-page">
    <!-- Navigation -->
    <nav class="nav-bar">
        <div class="nav-container">
            <a href="index.html" class="logo">
                <img src="S.png" alt="Logo" class="logo-img">
                <span class="logo-text">Snap<span class="logo-highlight">Bound</span></span>
            </a>
            <div class="nav-links">
                <a href="index.html#home" class="nav-link"><i class="fas fa-home"></i> Home</a>
                <a href="index.html#features" class="nav-link"><i class="fas fa-camera-retro"></i> Features</a>
                <a href="index.html#how-it-works" class="nav-link"><i class="fas fa-question-circle"></i> How It Works</a>
                <a href="index.html#about" class="nav-link"><i class="fas fa-info-circle"></i> About</a>
            </div>
            <div class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <div class="select-photos-container">
        <h1 class="page-title">Create Your <span>PhotoStrip</span></h1>
        
        <div class="selected-count">
            <i class="fas fa-image"></i> <span id="selectedCounter">0/4 photos selected</span>
        </div>
        
        <!-- Left Column - Photo Strip Preview -->
        <div class="strip-layout">
            <div class="preview-section">
                <div class="strip-preview" id="mainStrip">
                    <div class="photo-placeholder"></div>
                    <div class="photo-placeholder"></div>
                    <div class="photo-placeholder"></div>
                    <div class="photo-placeholder"></div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Options -->
        <div class="options-section">
            <!-- Color Options Card -->
            <div class="options-card color-options">
    <h2><i class="fas fa-palette"></i> Customize your PhotoStrip!</h2>
    
    
    <div class="color-palette">
        <div class="color-preview" style="background: #FF5757;" data-color="#FF5757"></div>
        <div class="color-preview" style="background: #4CAF50;" data-color="#4CAF50"></div>
        <div class="color-preview" style="background: #2196F3;" data-color="#2196F3"></div>
        <div class="color-preview" style="background: #9C27B0;" data-color="#9C27B0"></div>
        <div class="color-preview" style="background: #FF9800;" data-color="#FF9800"></div>
        <div class="color-preview" style="background: #607D8B;" data-color="#607D8B"></div>
        <button class="custom-color-btn" id="customColorBtn">
            <i class="fas fa-sliders-h"></i>
        </button>
    </div>
    <div class="color-value" style="margin-top: 15px;">
        Current: <span id="colorValue">#FF5757</span>
    </div>
</div>
            <!-- Add this after the color options section -->
<div class="options-card">
    <h2><i class="fas fa-magic"></i> Template Design</h2>
    <p>Choose a special theme for your photostrip</p>
    
    <div class="template-options">
        <div class="template-option" data-template="default">
            <div class="template-preview default-template">
                <div class="preview-strip">
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                </div>
            </div>
            <span>Default</span>
        </div>
        
        <div class="template-option" data-template="halloween">
            <div class="template-preview halloween-template">
                <div class="preview-strip">
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                </div>
                <div class="halloween-decoration">
                    <i class="fas fa-ghost"></i>
                    <i class="fas fa-pumpkin"></i>
                </div>
            </div>
            <span>Halloween</span>
        </div>
        
        <div class="template-option" data-template="vintage">
            <div class="template-preview vintage-template">
                <div class="preview-strip">
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                    <div class="preview-photo"></div>
                </div>
            </div>
            <span>Vintage</span>
        </div>
    </div>
    
    <button id="saveTemplateBtn" class="save-template-btn">
        <i class="fas fa-save"></i> Save Template Preference
    </button>
</div>

            
            <!-- Effect Options Card -->
            <div class="options-card">
                <h2><i class="fas fa-magic"></i> Photo Effect</h3>
                <div class="effect-options">
                    <div class="effect-circle active" data-effect="normal">
                        <i class="fas fa-image"></i>
                        <span>Normal</span>
                    </div>
                    <div class="effect-circle" data-effect="black">
                        <i class="fas fa-adjust"></i>
                        <span>B&W</span>
                    </div>
                    <div class="effect-circle" data-effect="sepia">
                        <i class="fas fa-sun"></i>
                        <span>Sepia</span>
                    </div>
                    <div class="effect-circle" data-effect="vintage">
                        <i class="fas fa-history"></i>
                        <span>Vintage</span>
                    </div>
                    <div class="effect-circle" data-effect="high">
                        <i class="fas fa-bolt"></i>
                        <span>Contrast</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Picker Modal -->
        <div class="color-modal" id="colorModal">
            <div class="modal-content">
                <button class="close-modal" id="closeModal">&times;</button>
                <h3>Custom Color</h3>
                <div id="colorPicker"></div>
                <div class="color-value" style="margin-top: 15px;">
                    Selected: <span id="modalColorValue">#FF5757</span>
                </div>
                <button id="applyColor" style="margin-top: 20px; width: 100%;">Apply Color</button>
            </div>
        </div>

        <h2 class="section-header">Select Your Photos</h2>
        <div class="photos-grid" id="photosGrid">
            <!-- Photos will be loaded here -->
        </div>

        <div class="selection-controls">
            <a href="picture.html" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Camera</a>
            <button id="downloadStrip"><i class="fas fa-download"></i> Download PhotoStrip</button>
            <button id="downloadPhotos"><i class="fas fa-images"></i> Download All Photos</button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="booth-footer">
        <div class="copyright">
            <p>&copy; 2025 SnapBound PhotoBooth. All rights reserved.</p>
        </div>
    </footer>

    <script src="index.js"></script>
    <script>
// Remove or comment out this code at the top of your script section:
// let pageLoadCount = parseInt(localStorage.getItem('pageLoadCount')) || 0;
// const MAX_PAGE_LOADS = 2;

// And replace the checkAndResetPhotos function with a simplified version:
function checkAndResetPhotos() {
    // Check if we have photos to work with
    const capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
    console.log('Available photos:', capturedImages.length);
    
    if (capturedImages.length === 0) {
        // No photos available, show message and redirect
        alert('No photos found! Please take some photos first.');
        window.location.href = 'picture.html';
    }
}

function updateStripPreviews() {
    const mainStrip = document.getElementById('mainStrip');
    mainStrip.innerHTML = ''; // Clear existing content
    
    // Set the strip background color immediately
    mainStrip.style.backgroundColor = selectedStrip;
    
    // Create 4 placeholders
    for (let i = 0; i < 4; i++) {
        const placeholder = document.createElement('div');
        placeholder.className = 'photo-placeholder';
        
        // Add selected photo if available
        if (selectedPhotos[i]) {
            const img = document.createElement('img');
            img.src = selectedPhotos[i].src;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            placeholder.appendChild(img);
        }
        
        mainStrip.appendChild(placeholder);
    }
    
    // Add text preview with automatic color contrast
    const textPreview = document.createElement('div');
    textPreview.className = 'strip-text-preview';
    textPreview.textContent = 'SnapBound';
    const isLight = isLightColor(selectedStrip);
    selectedTextColor = isLight ? '#333333' : '#ffffff';
    textPreview.style.color = selectedTextColor;
    mainStrip.appendChild(textPreview);
}

function displayCapturedImages() {
    const photosGrid = document.getElementById('photosGrid');
    if (!photosGrid) {
        console.error('Photos grid element not found!');
        return;
    }
    
    photosGrid.innerHTML = '';
    
    // Get images from localStorage with debugging
    try {
        const capturedImagesRaw = localStorage.getItem('capturedImages');
        console.log('Raw localStorage data:', capturedImagesRaw);
        
        if (!capturedImagesRaw) {
            console.error('No captured images data in localStorage');
            photosGrid.innerHTML = '<p style="text-align: center; padding: 40px; color: #666;">No photos available. Please capture some photos first.</p>';
            return;
        }
        
        const capturedImages = JSON.parse(capturedImagesRaw);
        console.log('Found images:', capturedImages.length);
        
        if (!Array.isArray(capturedImages) || capturedImages.length === 0) {
            photosGrid.innerHTML = '<p style="text-align: center; padding: 40px; color: #666;">No photos available. Please capture some photos first.</p>';
            return;
        }

        // Display each image
        capturedImages.forEach((imageData, index) => {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'photo-container';
            
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'photo-checkbox';
            checkbox.id = `photo-${index}`;
            
            checkbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    if (selectedPhotos.length >= MAX_PHOTOS) {
                        e.target.checked = false;
                        alert('You can only select 4 photos!');
                        return;
                    }
                    selectedPhotos.push({index, src: imageData});
                } else {
                    selectedPhotos = selectedPhotos.filter(photo => photo.index !== index);
                }
                updateStripPreviews();
                updateSelectedCounter();
            });
            
            const img = document.createElement('img');
            img.src = imageData;
            img.alt = `Photo ${index + 1}`;
            img.setAttribute('data-original-src', imageData);
            
            imgContainer.appendChild(checkbox);
            imgContainer.appendChild(img);
            photosGrid.appendChild(imgContainer);
        });
    } catch (err) {
        console.error('Error parsing captured images:', err);
        photosGrid.innerHTML = '<p style="text-align: center; padding: 40px; color: #666;">Error loading photos. Please try again.</p>';
    }
}

function updateSelectedCounter() {
    const counter = document.getElementById('selectedCounter');
    if (counter) {
        counter.textContent = `${selectedPhotos.length}/4 photos selected`;
    }
}

function clearPreviousPhotos() {
    selectedPhotos = [];
    updateStripPreviews();
    updateSelectedCounter();
}

// Add this to your existing global variables
let selectedStrip = '#FF5757';
let selectedEffect = 'normal';
let selectedPhotos = [];
let selectedTextColor = '#ffffff'; // Default white text
const MAX_PHOTOS = 4;

document.addEventListener('DOMContentLoaded', () => {
    // Add this at the start
    checkAndResetPhotos();
    
    // Initialize color picker in modal
    const colorPicker = new iro.ColorPicker("#colorPicker", {
        width: 280,
        color: selectedStrip,
        layout: [
            { 
                component: iro.ui.Wheel,
                options: {
                    borderWidth: 0,
                    wheelLightness: false,
                    wheelAngle: 0,
                    wheelDirection: 'anticlockwise'
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'value',
                    borderWidth: 0
                }
            }
        ]
    });

    // Modal functionality
    const colorModal = document.getElementById('colorModal');
    const customColorBtn = document.getElementById('customColorBtn');
    const closeModal = document.getElementById('closeModal');
    const applyColor = document.getElementById('applyColor');
    
    customColorBtn.addEventListener('click', () => {
        colorModal.classList.add('active');
    });
    
    closeModal.addEventListener('click', () => {
        colorModal.classList.remove('active');
    });
    
    applyColor.addEventListener('click', () => {
        selectedStrip = colorPicker.color.hexString;
        document.getElementById('colorValue').textContent = selectedStrip;
        updateStripPreviews();
        colorModal.classList.remove('active');
    });
    
    colorPicker.on('color:change', function(color) {
        document.getElementById('modalColorValue').textContent = color.hexString;
    });

    // Preset color selection
    document.querySelectorAll('.color-preview').forEach(preview => {
        preview.addEventListener('click', () => {
            selectedStrip = preview.getAttribute('data-color');
            document.getElementById('colorValue').textContent = selectedStrip;
            updateStripPreviews();
        });
    });

    // Effect selection
    document.querySelectorAll('.effect-circle').forEach(circle => {
        circle.addEventListener('click', () => {
            document.querySelectorAll('.effect-circle').forEach(c => 
                c.classList.remove('active'));
            circle.classList.add('active');
            selectedEffect = circle.getAttribute('data-effect');
            updatePreviewsWithEffect();
        });
    });

    // Display captured images immediately
    displayCapturedImages();

    // Download strip button
    document.getElementById('downloadStrip').addEventListener('click', () => {
        if (selectedPhotos.length !== 4) {
            alert('Please select exactly 4 photos');
            return;
        }
        const selectedImages = selectedPhotos.map(photo => photo.src);
        createPhotoStrip(selectedImages);
    });
    
    // Download all photos button
    document.getElementById('downloadPhotos').addEventListener('click', () => {
        const capturedImages = JSON.parse(localStorage.getItem('capturedImages')) || [];
        if (capturedImages.length === 0) {
            alert('No photos available to download');
            return;
        }
        
        // Create a zip file of all photos
        downloadAllPhotos(capturedImages);
    });

    // Initialize the page
    displayCapturedImages();
    updateSelectedCounter();
    updateStripPreviews();
    
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // Set initial text color based on the default strip color
    const isLightInitialColor = isLightColor(selectedStrip);
    selectedTextColor = isLightInitialColor ? '#333333' : '#ffffff';
});

function downloadAllPhotos(images) {
    // Simple implementation - download one by one
    // In a real app, you might want to use a library like JSZip to create a zip file
    images.forEach((imageData, index) => {
        const link = document.createElement('a');
        link.href = imageData;
        link.download = `snapbound-photo-${index+1}.jpg`;
        link.click();
    });
}

function applyEffectToImageData(imageData, effect) {
    const data = imageData.data;
    for (let i = 0; i < data.length; i += 4) {
        const r = data[i];
        const g = data[i + 1];
        const b = data[i + 2];
        
        switch(effect) {
            case 'black':
                const avg = (r + g + b) / 3;
                data[i] = data[i + 1] = data[i + 2] = avg;
                break;
            case 'sepia':
                data[i] = Math.min(255, (r * 0.393) + (g * 0.769) + (b * 0.189));
                data[i + 1] = Math.min(255, (r * 0.349) + (g * 0.686) + (b * 0.168));
                data[i + 2] = Math.min(255, (r * 0.272) + (g * 0.534) + (b * 0.131));
                break;
            case 'vintage':
                data[i] = Math.min(255, r * 1.1);
                data[i + 1] = Math.min(255, g * 0.9);
                data[i + 2] = Math.min(255, b * 0.9);
                break;
            case 'high':
                data[i] = Math.min(255, r * 1.5);
                data[i + 1] = Math.min(255, g * 1.5);
                data[i + 2] = Math.min(255, b * 1.5);
                break;
        }
    }
}

async function createPhotoStrip(images) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    canvas.width = 400;
    canvas.height = 1250;
    const cornerRadius = 15; // Adjust this value to change curve amount
    
    try {
        const photoWidth = 360;
        const photoHeight = 270;
        const xOffset = (canvas.width - photoWidth) / 2;
        const yGap = 15;

        // Fill background with selected color
        ctx.fillStyle = selectedStrip;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Load and draw each image with curved corners
        for (let i = 0; i < images.length; i++) {
            try {
                const img = await loadImage(images[i]);
                const yOffset = 25 + (i * (photoHeight + yGap));
                
                // Create curved corners
                ctx.save();
                ctx.beginPath();
                ctx.moveTo(xOffset + cornerRadius, yOffset);
                ctx.lineTo(xOffset + photoWidth - cornerRadius, yOffset);
                ctx.quadraticCurveTo(xOffset + photoWidth, yOffset, xOffset + photoWidth, yOffset + cornerRadius);
                ctx.lineTo(xOffset + photoWidth, yOffset + photoHeight - cornerRadius);
                ctx.quadraticCurveTo(xOffset + photoWidth, yOffset + photoHeight, xOffset + photoWidth - cornerRadius, yOffset + photoHeight);
                ctx.lineTo(xOffset + cornerRadius, yOffset + photoHeight);
                ctx.quadraticCurveTo(xOffset, yOffset + photoHeight, xOffset, yOffset + photoHeight - cornerRadius);
                ctx.lineTo(xOffset, yOffset + cornerRadius);
                ctx.quadraticCurveTo(xOffset, yOffset, xOffset + cornerRadius, yOffset);
                ctx.closePath();
                ctx.clip();

                // Draw the image
                ctx.drawImage(img, xOffset, yOffset, photoWidth, photoHeight);
                
                // Apply effect if selected
                if (selectedEffect !== 'normal') {
                    const imageData = ctx.getImageData(xOffset, yOffset, photoWidth, photoHeight);
                    applyEffectToImageData(imageData, selectedEffect);
                    ctx.putImageData(imageData, xOffset, yOffset);
                }
                
                // Add a subtle border
                ctx.strokeStyle = 'rgba(255,255,255,0.3)';
                ctx.lineWidth = 2;
                ctx.stroke();
                
                ctx.restore();
            } catch (err) {
                console.error('Error loading image:', err);
                throw new Error('Failed to load image');
            }
        }

        // Add watermark with adaptive text color and BIGGER TEXT SIZE
        ctx.fillStyle = selectedTextColor;
        ctx.font = 'bold 36px Poppins, sans-serif'; // Increased from 28px to 36px
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        
        // Add text with shadow for better visibility on any background
        if (selectedTextColor === '#ffffff') {
            // Add subtle shadow for white text
            ctx.shadowColor = 'rgba(0,0,0,0.5)';
            ctx.shadowBlur = 4;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 2;
        } else {
            // Add subtle shadow for dark text
            ctx.shadowColor = 'rgba(255,255,255,0.5)';
            ctx.shadowBlur = 4;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 1;
        }
        
        ctx.fillText('SnapBound', canvas.width / 2, canvas.height - 50); // Adjusted position
        
        // Reset shadow
        ctx.shadowColor = 'transparent';
        ctx.shadowBlur = 0;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 0;

        // Download using timestamp
        const timestamp = new Date().getTime();
        downloadCanvas(canvas, `photostrip-${timestamp}.jpg`);

    } catch (error) {
        console.error('Error creating photo strip:', error);
        alert('Error creating photo strip. Please try again.');
    }
}

function loadImage(src) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve(img);
        img.onerror = () => reject(new Error('Failed to load image'));
        img.src = src;
    });
}

function downloadCanvas(canvas, filename) {
    try {
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success notification
        showNotification('PhotoStrip downloaded successfully!');
    } catch (err) {
        console.error('Download error:', err);
        alert('Error downloading photo strip. Please try again.');
    }
}

// Add this function to show notifications
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

// Add this function to update previews with effects
function updatePreviewsWithEffect() {
    // Update strip preview images with the selected effect
    const stripImages = document.querySelectorAll('.photo-placeholder img');
    if (stripImages.length) {
        stripImages.forEach(img => {
            const originalSrc = img.getAttribute('data-original-src') || img.src;
            if (!img.getAttribute('data-original-src')) {
                img.setAttribute('data-original-src', originalSrc);
            }
            
            if (selectedEffect === 'normal') {
                img.src = originalSrc;
            } else {
                const tempImg = new Image();
                tempImg.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = this.naturalWidth;
                    canvas.height = this.naturalHeight;
                    
                    ctx.drawImage(this, 0, 0);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    applyEffectToImageData(imageData, selectedEffect);
                    ctx.putImageData(imageData, 0, 0);
                    
                    img.src = canvas.toDataURL('image/jpeg');
                };
                tempImg.src = originalSrc;
            }
        });
    }
}

// Add this function to your script section
function isLightColor(hexColor) {
    // Convert hex to RGB
    const hex = hexColor.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    
    // Calculate relative luminance using the formula for perceived brightness
    // https://www.w3.org/TR/WCAG20-TECHS/G18.html
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    
    // Return true if color is light (luminance > 0.5)
    return luminance > 0.5;
}

// Add this function to your script
function showDebugInfo() {
    // Create debug element
    const debugEl = document.createElement('div');
    debugEl.className = 'debug-bar';
    
    // Get localStorage info
    const capturedImagesRaw = localStorage.getItem('capturedImages');
    const imageCount = capturedImagesRaw ? JSON.parse(capturedImagesRaw).length : 0;
    
    // Create debug content
    debugEl.innerHTML = `
        <strong>Debug Info:</strong><br>
        localStorage items: ${localStorage.length}<br>
        capturedImages: ${imageCount} photos<br>
        selectedPhotos: ${selectedPhotos.length} selected<br>
        <button id="debugFixBtn">Fix Storage</button>
    `;
    
    // Add to body
    document.body.appendChild(debugEl);
    
    // Add fix button functionality
    document.getElementById('debugFixBtn').addEventListener('click', function() {
        // If there are no captured images but we know there should be
        if (imageCount === 0) {
            // Create some test images if needed
            const testImages = [
                'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9U6KKKABQFUAZOBz60YGc4HPrRRQB/9k='
            ];
            
            localStorage.setItem('capturedImages', JSON.stringify(testImages));
            showNotification('Added test image data. Refresh page.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(`Found ${imageCount} photos in storage`);
            setTimeout(() => location.reload(), 1500);
        }
    });
}

// Add this to your DOMContentLoaded event:
document.addEventListener('DOMContentLoaded', () => {
    // Add debug info after a short delay
    setTimeout(showDebugInfo, 1000);
    
    selectedStrip = '#FF5757';
    document.getElementById('colorValue').textContent = selectedStrip;
    updateStripPreviews();
});

// Smooth dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.querySelector('.dropdown-btn');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const selectedOption = document.querySelector('.selected-option');
    
    // Toggle dropdown
    dropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('active');
        dropdownMenu.classList.toggle('active');
    });
    
    // Select option
    dropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const text = this.querySelector('span').textContent;
            
            selectedOption.textContent = text;
            dropdownBtn.classList.remove('active');
            dropdownMenu.classList.remove('active');
            
            // You can add functionality here to handle template changes
            console.log('Selected template:', value);
            // updateStripTemplate(value); // You would implement this function
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.smooth-dropdown')) {
            dropdownBtn.classList.remove('active');
            dropdownMenu.classList.remove('active');
        }
    });
    
    // Close dropdown when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            dropdownBtn.classList.remove('active');
            dropdownMenu.classList.remove('active');
        }
    });
});
    </script>
</body>
</html>
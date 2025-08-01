<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Photos</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap');

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Fredoka', sans-serif;
            background-color: white;
        }

        .nav-bar {
            background-color: white;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
            padding-bottom: 100px;
            position: relative;
            z-index: 1;
            clear: both;
        }

        .photo-item {
            position: relative;
            aspect-ratio: 16/9;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease-in-out;
        }

        .photo-item.selected img {
            border: 3px solid #FF5757;
            transform: scale(0.98);
        }

        .action-buttons {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 1.5rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            text-align: center;
            z-index: 2;
        }

        .btn {
            padding: 1rem 3rem;
            margin: 0 1rem;
            border: none;
            border-radius: 25px;
            font-family: 'Fredoka', sans-serif;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #FF5757;
            color: white;
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .strip-options {
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 10px;
            position: relative;
            z-index: 2;
        }

        .strip-templates {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding: 1rem;
            margin-bottom: 1rem;
            height: auto;
        }

        .strip-template {
            min-width: 300px;
            height: 780px; /* Adjusted height */
            position: relative;
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0 auto;
            border: none;
            background-color: transparent; /* Remove any potential white backgrounds from the strip template */
        }

        .strip-template.selected {
            border: 3px solid #FF5757;
            transform: scale(0.98);
        }

        .photo-placeholder {
    position: absolute;
    width: 232px;
    height: 258px;
    right: 8px;
    background-color: transparent; /* Change from rgba(255, 255, 255, 0.1) to transparent */
    overflow: hidden;
    border-radius: 0;
    box-shadow: none;
}

.photo-placeholder img {
    width: calc(100% - 10px);
    height: calc(100% - 10px);
    object-fit: cover;
    display: block;
    border: none;
    background: transparent; /* Add transparent background */
    
}

        /* Fixed positions for photo placeholders */
        .photo-placeholder:nth-child(1) { 
    top: 21px; 
    right: 23px;
}
.photo-placeholder:nth-child(2) { 
    top: 197px; 
    right: 23px;
}
.photo-placeholder:nth-child(3) { 
    top: 372px; 
    right: 23px;
}
.photo-placeholder:nth-child(4) { 
    top: 547px; 
    right: 23px;
}

/* Classic Strip Style */
        .strip-template[data-template="classic"],
        .strip-template[data-template="modern"],
        .strip-template[data-template="vintage"] {
            background: none;
            border: none;
        }

        .strip-template[data-template="classic"] img {
            width: 90%;
            height: 150px;
            object-fit: cover;
            border: none;  /* Remove white border */
            box-shadow: none;
            margin: 5px 0;
            transition: all 0.3s;
        }

        /* Modern Strip Style */
        .strip-template[data-template="modern"] img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 0;  /* Remove border radius */
            margin: 8px 0;
            border: none;  /* Remove border */
        }

        /* Vintage Strip Style */
        .strip-template[data-template="vintage"] img {
            width: 85%;
            height: 150px;
            object-fit: cover;
            border: none;  /* Remove white border */
            box-shadow: none;
            transform: none;
            margin: 5px 0;
            transition: all 0.3s;
        }

        // Update the color-option styles
        .color-options {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* Show 4 colors per row */
    gap: 1rem;
    margin: 1rem auto;
    padding: 1rem;
    max-width: 800px; /* Limit maximum width */
}

.color-option {
    width: 100%;
    height: 40px;
    border-radius: 10px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.3s;
    margin: 0.5rem;
}

/* Add background colors for each option */
.color-option[data-color="White"] { 
    background-color: #FFFFFF; 
    border: 2px solid #e0e0e0; 
}
.color-option[data-color="Red"] { background-color: #FF5757; }
.color-option[data-color="Blue"] { background-color: #5785FF; }
.color-option[data-color="Yellow"] { background-color: #FFD757; }
.color-option[data-color="Purple"] { background-color: #9B57FF; }
.color-option[data-color="Green"] { background-color: #57FF8F; }
.color-option[data-color="Copper"] { background-color: #CD7F32; }

.color-option:hover {
    transform: scale(1.05);
}

.color-option.selected {
    border: 2px solid #333;
    transform: scale(0.98);
}

/* Effect options styles */
.effect-options {
    display: flex;
    gap: 1rem;
    margin: 1rem auto;
    padding: 1rem;
    max-width: 800px;
    flex-wrap: wrap;
    justify-content: center;
}

.effect-option {
    padding: 0.5rem 1.5rem;
    border-radius: 20px;
    cursor: pointer;
    background: #f0f0f0;
    transition: all 0.3s;
    border: 2px solid transparent;
}

/* Specific effect styles */
.effect-option[data-effect="none"] {
    background-color: #e0e0e0;
}
.effect-option[data-effect="grayscale"] {
    background-color: #757575;
}
.effect-option[data-effect="sepia"] {
    background-color: #704214;
}
.effect-option[data-effect="vintage"] {
    background-color: #9B57FF;
}
.effect-option[data-effect="contrast"] {
    background-color: #FF5757;
    color: white;
}

.effect-option:hover {
    transform: scale(1.05);
}

.effect-option.selected {
    background: #FF5757;
    color: white;
    border: 2px solid #FF5757;
}
    </style>
</head>
<body>
    <nav class="nav-bar">
        <div class="nav-container">
            <a href="home.php" class="logo">PhotoBooth</a>
        </div>
    </nav>

    <div class="container">
        <h1>Select Your Photos</h1>
        <p>Click on the photos you want to keep. Selected photos will be highlighted.</p>
        
        <div class="strip-options">
            <h2>Choose Your Strip Style</h2>
            
            <div class="strip-templates">
                <div class="strip-template" data-template="classic">
                    <div class="photo-placeholder">
                        <img src="placeholder.jpg" alt="Photo 1">
                    </div>
                    <div class="photo-placeholder">
                        <img src="placeholder.jpg" alt="Photo 2">
                    </div>
                    <div class="photo-placeholder">
                        <img src="placeholder.jpg" alt="Photo 3">
                    </div>
                    <div class="photo-placeholder">
                        <img src="placeholder.jpg" alt="Photo 4">
                    </div>
                </div>
            </div>

            <h3>Choose Strip Color</h3>
            <div class="color-options">
                <div class="color-option selected" data-color="White" title="White"></div>
                <div class="color-option" data-color="Red" title="Red"></div>
                <div class="color-option" data-color="Blue" title="Blue"></div>
                <div class="color-option" data-color="Yellow" title="Yellow"></div>
                <div class="color-option" data-color="Purple" title="Purple"></div>
                <div class="color-option" data-color="Green" title="Green"></div>
                <div class="color-option" data-color="Copper" title="Copper"></div>
            </div>

            <h3>Choose Photo Effect</h3>
            <div class="effect-options">
                <div class="effect-option selected" data-effect="none">Normal</div>
                <div class="effect-option" data-effect="grayscale">Black & White</div>
                <div class="effect-option" data-effect="sepia">Sepia</div>
                <div class="effect-option" data-effect="vintage">Vintage</div>
                <div class="effect-option" data-effect="contrast">High Contrast</div>
            </div>
        </div>

        <div class="photos-grid" id="photosGrid">
            <!-- Photos will be loaded here -->
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" id="downloadBtn">Download Selected</button>
            <button class="btn btn-primary" id="downloadStripBtn">Download PhotoStrip</button>
            <button class="btn btn-secondary" onclick="window.location.href='Picture.php'">Take New Photos</button>
        </div>
    </div>

    <script>
        // First, define downloadPhotoStrip outside of DOMContentLoaded
        let selectedColor = '#FF5757'; // Default color
        let selectedEffect = 'none'; // Default effect

        function downloadPhotoStrip() {
            const canvas = document.createElement('canvas');
            canvas.width = 300;
            canvas.height = 780;
            const ctx = canvas.getContext('2d');

            return new Promise((resolve) => {
                // First draw the background template
                const bgImage = new Image();
                bgImage.crossOrigin = "anonymous";
                const selectedColorName = document.querySelector('.color-option.selected').dataset.color;
                bgImage.src = `Photo/${selectedColorName}.png`;
                
                bgImage.onload = () => {
                    // Draw background first
                    ctx.drawImage(bgImage, 0, 0, canvas.width, canvas.height);

                    // Then draw the photos on top
                    const photos = JSON.parse(localStorage.getItem('capturedPhotos') || '[]');
                    const photoPromises = photos.slice(0, 4).map(photoData => {
                        return new Promise((resolve) => {
                            const img = new Image();
                            img.crossOrigin = "anonymous";
                            img.src = photoData;
                            img.onload = () => resolve(img);
                        });
                    });

                    Promise.all(photoPromises).then(images => {
                        const positions = [
                            { top: 21, right: 27, width: 245, height: 158 },
                            { top: 197, right: 27, width: 245, height: 158 },
                            { top: 372, right: 27, width: 245, height: 158 },
                            { top: 547, right: 27, width: 245, height: 158 }
                        ];

                        // Draw photos after background
                        images.forEach((img, i) => {
                            const pos = positions[i];
                            const x = canvas.width - pos.right - pos.width;
                            ctx.drawImage(img, x, pos.top, pos.width, pos.height);
                            
                            // Apply selected effect to each photo
                            const selectedEffect = document.querySelector('.effect-option.selected').dataset.effect;
                            if (selectedEffect !== 'none') {
                                const imageArea = {
                                    x: x,
                                    y: pos.top,
                                    width: pos.width,
                                    height: pos.height
                                };
                                const tempCanvas = document.createElement('canvas');
                                tempCanvas.width = pos.width;
                                tempCanvas.height = pos.height;
                                const tempCtx = tempCanvas.getContext('2d');
                                tempCtx.drawImage(img, 0, 0, pos.width, pos.height);
                                applyEffect(tempCtx, selectedEffect);
                                ctx.drawImage(tempCanvas, x, pos.top, pos.width, pos.height);
                            }
                        });

                        const link = document.createElement('a');
                        link.download = 'photostrip.png';
                        link.href = canvas.toDataURL('image/png');
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        resolve();
                    });
                };
            });
        }

        function applyEffect(ctx, effect) {
    const imageData = ctx.getImageData(0, 0, ctx.canvas.width, ctx.canvas.height);
    const data = imageData.data;

    switch (effect) {
        case 'grayscale':
            for (let i = 0; i < data.length; i += 4) {
                const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
                data[i] = avg;     // red
                data[i + 1] = avg; // green
                data[i + 2] = avg; // blue
            }
            break;

        case 'sepia':
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                data[i] = (r * 0.393) + (g * 0.769) + (b * 0.189);     // red
                data[i + 1] = (r * 0.349) + (g * 0.686) + (b * 0.168); // green
                data[i + 2] = (r * 0.272) + (g * 0.534) + (b * 0.131); // blue
            }
            break;

        case 'vintage':
            for (let i = 0; i < data.length; i += 4) {
                data[i] *= 1.2;     // Increase red
                data[i + 2] *= 0.8; // Decrease blue
                data[i + 1] *= 0.9; // Slightly decrease green
            }
            break;

        case 'contrast':
            const factor = 1.5;
            for (let i = 0; i < data.length; i += 4) {
                data[i] = factor * (data[i] - 128) + 128;     // red
                data[i + 1] = factor * (data[i + 1] - 128) + 128; // green
                data[i + 2] = factor * (data[i + 2] - 128) + 128; // blue
            }
            break;
    }

    ctx.putImageData(imageData, 0, 0);
}

        document.addEventListener('DOMContentLoaded', () => {
            // Set default white color
            const whiteOption = document.querySelector('.color-option[data-color="White"]');
            whiteOption.classList.add('selected');
            selectedColor = 'White';
            updatePhotoStrip();

            const photos = JSON.parse(localStorage.getItem('capturedPhotos') || '[]');
            const photosGrid = document.getElementById('photosGrid');
            const downloadBtn = document.getElementById('downloadBtn');
            const selectedPhotos = new Set();

            photos.forEach((photoData, index) => {
                const photoDiv = document.createElement('div');
                photoDiv.className = 'photo-item';
                
                const img = document.createElement('img');
                img.src = photoData;
                photoDiv.appendChild(img);

                photoDiv.addEventListener('click', () => {
                    photoDiv.classList.toggle('selected');
                    if (selectedPhotos.has(index)) {
                        selectedPhotos.delete(index);
                    } else {
                        selectedPhotos.add(index);
                    }
                    downloadBtn.disabled = selectedPhotos.size === 0;
                    
                    // Update preview immediately when a photo is selected
                    updateStripTemplates();
                    updatePhotoStrip();
                });

                photosGrid.appendChild(photoDiv);
            });

            function updateStripTemplates() {
                const photos = JSON.parse(localStorage.getItem('capturedPhotos') || '[]');
                const placeholders = document.querySelectorAll('.photo-placeholder img');
                const selectedEffect = document.querySelector('.effect-option.selected').dataset.effect;
                
                // Update each placeholder with selected photos and effects
                placeholders.forEach((placeholder, index) => {
                    if (index < photos.length) {
                        // Create a temporary canvas to apply effects
                        const tempCanvas = document.createElement('canvas');
                        const tempCtx = tempCanvas.getContext('2d');
                        const img = new Image();
                        
                        img.onload = () => {
                            tempCanvas.width = img.width;
                            tempCanvas.height = img.height;
                            tempCtx.drawImage(img, 0, 0);
                            
                            // Apply selected effect
                            if (selectedEffect !== 'none') {
                                applyEffect(tempCtx, selectedEffect);
                            }
                            
                            // Update placeholder with effected image
                            placeholder.src = tempCanvas.toDataURL();
                        };
                        
                        img.src = photos[index];
                    } else {
                        placeholder.src = 'placeholder.jpg';
                    }
                });
            }

            // Update the download button event listener
            downloadBtn.addEventListener('click', () => {
                if (selectedPhotos.size > 0) {
                    // Download individual photos
                    selectedPhotos.forEach(index => {
                        const link = document.createElement('a');
                        link.href = photos[index];
                        link.download = `photo_${index + 1}.jpg`;
                        link.click();
                    });
                }
                // Add download PhotoStrip button
                downloadPhotoStrip();
            });

            // Strip template selection
            const templates = document.querySelectorAll('.strip-template');
            const colorOptions = document.querySelectorAll('.color-option');
            const effectOptions = document.querySelectorAll('.effect-option');
            let selectedTemplate = 'classic';

            templates.forEach(template => {
                template.addEventListener('click', () => {
                    templates.forEach(t => t.classList.remove('selected'));
                    template.classList.add('selected');
                    selectedTemplate = template.dataset.template;
                    updatePhotoStrip();
                });
            });

            colorOptions.forEach(option => {
                option.addEventListener('click', () => {
                    colorOptions.forEach(o => o.classList.remove('selected'));
                    option.classList.add('selected');
                    selectedColor = option.dataset.color;
                    updatePhotoStrip();
                });
            });

            effectOptions.forEach(option => {
                option.addEventListener('click', () => {
                    effectOptions.forEach(o => o.classList.remove('selected'));
                    option.classList.add('selected');
                    updateStripTemplates(); // This will now apply effects to preview
                    updatePhotoStrip();
                });
            });

            function updatePhotoStrip() {
                const stripTemplate = document.querySelector('.strip-template');
                const selectedColorName = document.querySelector('.color-option.selected').dataset.color;
                
                // Update background image based on selected color
                stripTemplate.style.cssText = `
                    background: url('Photo/${selectedColorName}.png') no-repeat center center;
                    background-size: contain;
                `;
            }

            // Add this function to calculate hue rotation based on selected color
            function getHueRotation(hexColor) {
                // Convert hex to RGB
                const r = parseInt(hexColor.slice(1, 3), 16);
                const g = parseInt(hexColor.slice(3, 5), 16);
                const b = parseInt(hexColor.slice(5, 7), 16);
                
                // Calculate hue
                const hue = Math.atan2(
                    Math.sqrt(3) * (g - b),
                    2 * r - g - b
                ) * (180 / Math.PI);
                
                return `${hue}deg`;
            }
        });

        // Update the event listener
        document.getElementById('downloadStripBtn').addEventListener('click', () => {
            downloadPhotoStrip().catch(error => {
                console.error('Error creating photo strip:', error);
                alert('Error creating photo strip. Please try again.');
            });
        });
    </script>
</body>
</html>
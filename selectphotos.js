let selectedColor = '#FF5757';
let selectedEffect = 'none';

function downloadPhotoStrip() {
    const canvas = document.createElement('canvas');
    canvas.width = 300;
    canvas.height = 780;
    const ctx = canvas.getContext('2d');

    return new Promise((resolve) => {
        const bgImage = new Image();
        bgImage.crossOrigin = "anonymous";
        const selectedColorName = document.querySelector('.color-option.selected').dataset.color;
        // Update path to match your file names directly
        bgImage.src = `Photo/${selectedColorName}.png`;
        
        bgImage.onload = () => {
            ctx.drawImage(bgImage, 0, 0, canvas.width, canvas.height);

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

                images.forEach((img, i) => {
                    const pos = positions[i];
                    const x = canvas.width - pos.right - pos.width;
                    ctx.drawImage(img, x, pos.top, pos.width, pos.height);
                    
                    const selectedEffect = document.querySelector('.effect-option.selected').dataset.effect;
                    if (selectedEffect !== 'none') {
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
                data[i] = avg;
                data[i + 1] = avg;
                data[i + 2] = avg;
            }
            break;
        case 'sepia':
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                data[i] = (r * 0.393) + (g * 0.769) + (b * 0.189);
                data[i + 1] = (r * 0.349) + (g * 0.686) + (b * 0.168);
                data[i + 2] = (r * 0.272) + (g * 0.534) + (b * 0.131);
            }
            break;
        case 'vintage':
            for (let i = 0; i < data.length; i += 4) {
                data[i] *= 1.2;
                data[i + 2] *= 0.8;
                data[i + 1] *= 0.9;
            }
            break;
        case 'contrast':
            const factor = 1.5;
            for (let i = 0; i < data.length; i += 4) {
                data[i] = factor * (data[i] - 128) + 128;
                data[i + 1] = factor * (data[i + 1] - 128) + 128;
                data[i + 2] = factor * (data[i + 2] - 128) + 128;
            }
            break;
    }
    ctx.putImageData(imageData, 0, 0);
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize your event listeners and other functionality here
    // ... (rest of your existing JavaScript code)
});

// Add download button event listener
document.getElementById('downloadStripBtn').addEventListener('click', () => {
    downloadPhotoStrip().catch(error => {
        console.error('Error creating photo strip:', error);
        alert('Error creating photo strip. Please try again.');
    });
});

// Add this function to update the preview
function updatePhotoStrip() {
    const stripTemplate = document.querySelector('.strip-template');
    const selectedColorName = document.querySelector('.color-option.selected').dataset.color;
    
    // Update background image based on selected color with new path
    stripTemplate.style.cssText = `
        background: url('Photo/colored_strips/${selectedColorName}.png') no-repeat center center;
        background-size: contain;
    `;
}

// Update color options click handler
document.querySelectorAll('.color-option').forEach(option => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
        option.classList.add('selected');
        selectedColor = option.dataset.color;
        updatePhotoStrip();
    });
});
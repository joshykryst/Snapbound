async function createPhotoStrip(images) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    // Match the dimensions from the preview strip in the HTML
    canvas.width = 200;  // Same as strip-preview width in HTML
    canvas.height = 650; // Same as strip-preview height in HTML
    const cornerRadius = 8;  // For individual photos
    const outerCornerRadius = 15; // For the entire strip
    
    try {
        // Create a temporary canvas for the rounded rectangle background
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = canvas.width;
        tempCanvas.height = canvas.height;
        const tempCtx = tempCanvas.getContext('2d');
        
        // Draw rounded rectangle for the entire strip
        tempCtx.beginPath();
        tempCtx.moveTo(outerCornerRadius, 0);
        tempCtx.lineTo(canvas.width - outerCornerRadius, 0);
        tempCtx.quadraticCurveTo(canvas.width, 0, canvas.width, outerCornerRadius);
        tempCtx.lineTo(canvas.width, canvas.height - outerCornerRadius);
        tempCtx.quadraticCurveTo(canvas.width, canvas.height, canvas.width - outerCornerRadius, canvas.height);
        tempCtx.lineTo(outerCornerRadius, canvas.height);
        tempCtx.quadraticCurveTo(0, canvas.height, 0, canvas.height - outerCornerRadius);
        tempCtx.lineTo(0, outerCornerRadius);
        tempCtx.quadraticCurveTo(0, 0, outerCornerRadius, 0);
        tempCtx.closePath();
        
        // Fill the rounded rectangle with the selected background color
        tempCtx.fillStyle = selectedStrip;
        tempCtx.fill();
        
        // Now draw on our main canvas
        ctx.drawImage(tempCanvas, 0, 0);
        
        // Adjust photo size to match the preview in HTML
        const photoWidth = 160;  // Width of photos in preview
        const photoHeight = 110; // Height of photos in preview
        const xOffset = (canvas.width - photoWidth) / 2;
        const topMargin = 25; // Increased top margin
        const yGap = 40; // Significantly increased spacing between photos

        // Load and draw each image with curved corners
        for (let i = 0; i < images.length; i++) {
            try {
                const img = await loadImage(images[i]);
                const yOffset = topMargin + (i * (photoHeight + yGap));
                
                // Create curved corners for individual photos
                ctx.save();
                ctx.beginPath();
                ctx.moveTo(xOffset + cornerRadius, yOffset);
                ctx.lineTo(xOffset + photoWidth - cornerRadius, yOffset);
                ctx.quadraticCurveTo(xOffset + photoWidth, yOffset, xOffset + photoWidth, yOffset + cornerRadius);
                ctx.lineTo(xOffset + photoWidth, yOffset + photoHeight - cornerRadius);
                ctx.quadraticCurveTo(xOffset + photoWidth, yOffset + photoHeight, xOffset + photoWidth - cornerRadius, yOffset + photoHeight);
                ctx.lineTo(xOffset + cornerRadius, yOffset + photoHeight);
                ctx.quadraticCurveTo(xOffset, yOffset + photoHeight, xOffset, yOffset + cornerRadius);
                ctx.lineTo(xOffset, yOffset + cornerRadius);
                ctx.quadraticCurveTo(xOffset, yOffset, xOffset + cornerRadius, yOffset);
                ctx.closePath();
                ctx.clip();

                // Calculate dimensions to maintain aspect ratio
                const imgAspect = img.naturalWidth / img.naturalHeight;
                const frameAspect = photoWidth / photoHeight;
                
                let drawWidth, drawHeight, offsetX, offsetY;
                
                if (imgAspect > frameAspect) {
                    // Image is wider than frame (relative to heights)
                    drawWidth = photoWidth;
                    drawHeight = drawWidth / imgAspect;
                    offsetX = xOffset;
                    offsetY = yOffset + (photoHeight - drawHeight) / 2;
                } else {
                    // Image is taller than frame (relative to widths)
                    drawHeight = photoHeight;
                    drawWidth = drawHeight * imgAspect;
                    offsetX = xOffset + (photoWidth - drawWidth) / 2;
                    offsetY = yOffset;
                }
                
                // Draw image with preserved aspect ratio
                ctx.drawImage(img, offsetX, offsetY, drawWidth, drawHeight);
                ctx.restore();
            } catch (err) {
                console.error('Error processing image:', err);
            }
        }

        // Use the same font as in the HTML preview
        // Load Poppins font explicitly
        document.fonts.ready.then(() => {
            // Add watermark text with the Poppins font
            ctx.fillStyle = selectedTextColor;
            ctx.font = 'bold 24px "Poppins", sans-serif';  // Match font in HTML
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Add text shadow for better visibility
            if (selectedTextColor === '#ffffff') {
                ctx.shadowColor = 'rgba(0,0,0,0.5)';
                ctx.shadowBlur = 2;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 1;
            } else {
                ctx.shadowColor = 'rgba(255,255,255,0.5)';
                ctx.shadowBlur = 2;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 1;
            }
            
            // Add this line for debugging
            console.log('Adding watermark: Unpacked');

            // Position text at the bottom with proper spacing
            ctx.fillText('Unpacked', stripWidth / 2, stripHeight - 20);

            // Reset shadow
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
            
            // Download the completed strip
            const timestamp = new Date().getTime();
            downloadCanvas(canvas, `photostrip-${timestamp}.jpg`);
        });

    } catch (error) {
        console.error('Error creating photo strip:', error);
        alert('Error creating photo strip. Please try again.');
    }
}
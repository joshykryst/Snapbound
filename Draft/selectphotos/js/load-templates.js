async function loadAvailableTemplates() {
    try {
        const response = await fetch('/api/templates');
        const templates = await response.json();
        
        // Add templates to your selection interface
        const templateSelector = document.getElementById('templateSelector');
        templateSelector.innerHTML = templates.map(template => `
            <div class="template-option">
                <img src="${template.url}" alt="${template.name}">
            </div>
        `).join('');
    } catch (err) {
        console.error('Error loading templates:', err);
    }
}
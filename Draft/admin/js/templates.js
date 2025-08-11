async function saveTemplate(template) {
    try {
        const response = await fetch('/api/templates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(template)
        });
        
        if (!response.ok) {
            throw new Error('Failed to save template');
        }
        
        alert('Template uploaded successfully');
        await loadTemplates();
    } catch (err) {
        console.error('Error saving template:', err);
        alert('Failed to save template');
    }
}

async function loadTemplates() {
    try {
        const response = await fetch('/api/templates');
        const templates = await response.json();
        
        const grid = document.getElementById('templateGrid');
        
        if (templates.length === 0) {
            grid.innerHTML = '<p>No templates uploaded yet</p>';
            return;
        }
        
        grid.innerHTML = templates.map(template => `
            <div class="template-item">
                <img src="${template.url}" alt="${template.name}">
                <div class="template-actions">
                    <button onclick="deleteTemplate('${template.id}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `).join('');
    } catch (err) {
        console.error('Error loading templates:', err);
        grid.innerHTML = '<p>Error loading templates</p>';
    }
}
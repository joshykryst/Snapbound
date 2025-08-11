class TemplateManager {
    constructor() {
        this.apiUrl = 'api/templates.php';
    }

    async addTemplate(template) {
        const formData = new FormData();
        formData.append('template', template.file);
        formData.append('name', template.name);
        formData.append('is_customizable', template.isCustomizable);
        formData.append('spacing', template.spacing);

        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.error || 'Failed to save template');
            }
            return data;
        } catch (error) {
            console.error('Error adding template:', error);
            throw error;
        }
    }

    async getTemplates() {
        try {
            const response = await fetch(this.apiUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching templates:', error);
            return [];
        }
    }

    async deleteTemplate(id) {
        try {
            const response = await fetch(`${this.apiUrl}?id=${id}`, {
                method: 'DELETE'
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.error || 'Failed to delete template');
            }
            return data;
        } catch (error) {
            console.error('Error deleting template:', error);
            throw error;
        }
    }
}
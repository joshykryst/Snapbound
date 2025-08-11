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
            return await response.json();
        } catch (error) {
            console.error('Error adding template:', error);
            throw error;
        }
    }

    async getTemplates() {
        try {
            const response = await fetch(this.apiUrl);
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
            return await response.json();
        } catch (error) {
            console.error('Error deleting template:', error);
            throw error;
        }
    }
}
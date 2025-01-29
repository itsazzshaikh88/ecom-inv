class CKEditorInstance {
    constructor(elementId, config = {}) {
        this.elementId = elementId;
        this.editor = null;
        this.config = config;
    }

    /**
     * Initialize CKEditor for the given element.
     * @returns {Promise<void>}
     */
    async initialize() {
        const element = document.getElementById(this.elementId);
        if (!element) {
            throw new Error(`Element with ID #${this.elementId} not found.`);
        }

        try {
            this.editor = await ClassicEditor.create(element, this.config);
            console.log(`CKEditor initialized for #${this.elementId}`);
        } catch (error) {
            console.error(`Failed to initialize CKEditor for #${this.elementId}:`, error);
        }
    }

    /**
     * Destroy the CKEditor instance.
     * @returns {Promise<void>}
     */
    async destroy() {
        if (!this.editor) {
            console.warn(`No editor instance found for #${this.elementId} to destroy.`);
            return;
        }

        try {
            await this.editor.destroy();
            this.editor = null;
            console.log(`CKEditor destroyed for #${this.elementId}`);
        } catch (error) {
            console.error(`Failed to destroy CKEditor for #${this.elementId}:`, error);
        }
    }

    /**
     * Set content in the CKEditor instance.
     * @param {string} content - The content to set.
     */
    setContent(content) {
        if (!this.editor) {
            console.warn(`No editor instance found for #${this.elementId}.`);
            return;
        }

        this.editor.setData(content);
        console.log(`Content set for #${this.elementId}`);
    }

    /**
     * Get content from the CKEditor instance.
     * @returns {string|null} - The content of the editor or null if not initialized.
     */
    getContent() {
        if (!this.editor) {
            console.warn(`No editor instance found for #${this.elementId}.`);
            return null;
        }

        return this.editor.getData();
    }
}

// File Upload Manager with proper FormData integration
class FileUploadManager {
    constructor(modalId) {
        this.modalId = modalId;
        this.modalElement = document.getElementById(modalId);
        this.fileInput = null;
        this.filesList = null;
        this.emptyState = null;
        this.files = new Map(); // Store files with metadata
        this.maxFileSize = 10 * 1024 * 1024; // 10MB

        this.init();
    }

    init() {
        // Initialize when modal is shown
        if (this.modalElement) {
            this.modalElement.addEventListener('shown.bs.modal', () => {
                this.initElements();
                this.initEventListeners();
            });
        }
    }

    initElements() {
        // Get elements within modal
        this.fileInput = this.modalElement.querySelector('#attachments');
        this.filesList = this.modalElement.querySelector('#filesList');
        this.emptyState = this.modalElement.querySelector('#emptyState');

        // Create a hidden file input for actual file storage
        this.createHiddenFileInput();

        // Update UI
        this.updateUI();
    }

    createHiddenFileInput() {
        // Remove existing hidden input if any
        const existingHidden = this.modalElement.querySelector('#hiddenAttachmentsInput');
        if (existingHidden) {
            existingHidden.remove();
        }

        // Create a new hidden file input
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'file';
        hiddenInput.id = 'hiddenAttachmentsInput';
        hiddenInput.name = 'attachments[]';
        hiddenInput.multiple = true;
        hiddenInput.style.display = 'none';

        this.modalElement.appendChild(hiddenInput);
        this.hiddenInput = hiddenInput;

        // Also update the main file input to clear on change
        if (this.fileInput) {
            this.fileInput.addEventListener('change', (e) => {
                this.handleFileSelection(e.target.files);
                e.target.value = ''; // Clear the visible input
            });
        }
    }

    initEventListeners() {
        // Add Files Button Click
        const addFilesBtn = this.modalElement.querySelector('#addFilesBtn');
        if (addFilesBtn) {
            addFilesBtn.addEventListener('click', () => {
                this.fileInput.click();
            });
        }

        // Drag and Drop
        const dropZone = this.modalElement.querySelector('#filesListContainer');
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.backgroundColor = '#e9ecef';
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.style.backgroundColor = '';
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.backgroundColor = '';
                this.handleFileSelection(e.dataTransfer.files);
            });
        }
    }

    handleFileSelection(fileList) {
        Array.from(fileList).forEach(file => {
            if (this.validateFile(file)) {
                this.addFile(file);
            }
        });
        this.updateUI();
        this.updateHiddenInput();
    }

    validateFile(file) {
        // Check file size
        if (file.size > this.maxFileSize) {
            alert(`File "${file.name}" exceeds 10MB limit`);
            return false;
        }

        // Check if file already exists
        if (this.files.has(file.name + file.size)) {
            alert(`File "${file.name}" is already added`);
            return false;
        }

        return true;
    }

    addFile(file) {
        const fileId = Date.now() + Math.random();
        this.files.set(fileId, {
            id: fileId,
            file: file,
            name: file.name,
            size: file.size,
            type: file.type,
            uploaded: false
        });
    }

    removeFile(fileId) {
        this.files.delete(fileId);
        this.updateUI();
        this.updateHiddenInput();
    }

    updateUI() {
        if (!this.filesList || !this.emptyState) return;

        this.filesList.innerHTML = '';

        if (this.files.size === 0) {
            this.emptyState.style.display = 'block';
        } else {
            this.emptyState.style.display = 'none';

            this.files.forEach((fileData, fileId) => {
                const fileElement = this.createFileElement(fileData);
                this.filesList.appendChild(fileElement);
            });
        }
    }

    updateHiddenInput() {
        // This is a WORKAROUND since we can't directly set files on input
        // We'll handle this in the FormData preparation instead
    }

    createFileElement(fileData) {
        const div = document.createElement('div');
        div.className = 'file-item';
        div.dataset.fileId = fileData.id;

        const fileIcon = this.getFileIcon(fileData);
        const fileSize = this.formatFileSize(fileData.size);

        div.innerHTML = `
            <div class="file-item-header">
                <div class="file-icon">
                    <i class="${fileIcon.class} fa-lg" style="color: ${fileIcon.color}"></i>
                </div>
                <div class="file-info">
                    <div class="file-name text-truncate" title="${fileData.name}">
                        ${fileData.name}
                    </div>
                    <div class="file-size">${fileSize}</div>
                </div>
                <button type="button" class="file-remove-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Add event listener to remove button
        const removeBtn = div.querySelector('.file-remove-btn');
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeFile(fileData.id);
        });

        return div;
    }

    getFileIcon(fileData) {
        const type = fileData.type;
        const name = fileData.name.toLowerCase();

        if (type.startsWith('image/')) {
            return { class: 'fas fa-image', color: '#4CAF50' };
        } else if (type.includes('pdf')) {
            return { class: 'fas fa-file-pdf', color: '#F44336' };
        } else if (type.includes('word') || name.includes('.doc')) {
            return { class: 'fas fa-file-word', color: '#2196F3' };
        } else if (type.includes('excel') || type.includes('spreadsheet') || name.includes('.xls')) {
            return { class: 'fas fa-file-excel', color: '#4CAF50' };
        } else if (type.includes('zip') || type.includes('compressed')) {
            return { class: 'fas fa-file-archive', color: '#FF9800' };
        } else {
            return { class: 'fas fa-file', color: '#9E9E9E' };
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    getFiles() {
        return Array.from(this.files.values()).map(f => f.file);
    }

    clearFiles() {
        this.files.clear();
        this.updateUI();
    }
}

// Initialize file manager when modal opens
document.addEventListener('DOMContentLoaded', function () {
    // Event delegation for all modals
    document.addEventListener('shown.bs.modal', function (event) {
        const modal = event.target;
        const modalId = modal.id;

        // Initialize file manager for this modal
        if (!window.fileManagers) window.fileManagers = {};
        if (!window.fileManagers[modalId]) {
            window.fileManagers[modalId] = new FileUploadManager(modalId);
        }
    });
});
window.initializeFileUpload = function (modalId) {

    if (!window.fileManagers) window.fileManagers = {};
    if (!window.fileManagers[modalId]) {
        window.fileManagers[modalId] = new FileUploadManager(modalId);
    }
    return window.fileManager;
};
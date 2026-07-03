
/**
 * Dynamic AJAX Library with Modal Support
 * Handles all AJAX operations with reusable modals
 */
class AjaxLibrary {
    constructor() {
        this.baseUrl = window.location.origin;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        this.csrfHeader = document.querySelector('meta[name="csrf-header"]')?.content || 'X-CSRF-TOKEN';
        this.defaultHeaders = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };

        if (this.csrfToken) {
            this.defaultHeaders[this.csrfHeader] = this.csrfToken;
        }

        // Initialize toast notifications if available
        this.initNotifications();

        // Bind global click handlers
        this.bindGlobalHandlers();
    }

    /**
     * Initialize notification system
     */
    initNotifications() {
        // Create notification container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

    }

    /**
     * Bind global click handlers for AJAX links and forms
     */
    bindGlobalHandlers() {
        // Handle AJAX form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.classList.contains('ajax-form')) {
                e.preventDefault();
                this.submitForm(form);
            }
        });

        // Handle AJAX links
        document.addEventListener('click', (e) => {

            const button = e.target.closest('button');

            // if (button && button.classList.contains('ajax-link')) {
            //     e.preventDefault();
            //     this.handleAjaxLink(button);
            // }

            if (button && button.classList.contains('modal-trigger')) {
                let urlToShow = button.getAttribute('data-url');
                this.closeModal();//close previous model

                setTimeout(() => {
                    let to_init_select2 = false;
                    let elementToInit = '';
                    if (button.classList.contains('init_select_up')) {
                        elementToInit = button.getAttribute('data-select2_init');
                        to_init_select2 = true;
                    }
                    this.openModal(urlToShow, button.dataset, to_init_select2, elementToInit);
                }, 500);
            }

            const link = e.target.closest('a');

            if (link && link.classList.contains('ajax-link')) {
                e.preventDefault();
                this.handleAjaxLink(link);
            }


            // Handle modal triggers
            if (link && link.classList.contains('modal-trigger')) {
                e.preventDefault();
                this.closeModal();//close previous model
                setTimeout(() => {
                    let to_init_select2 = false;
                    let elementToInit = '';
                    if (link.classList.contains('init_select_up')) {
                        elementToInit = link.getAttribute('data-select2_init');
                        to_init_select2 = true;
                    }
                    this.openModal(link.href, link.dataset, to_init_select2, elementToInit);
                }, 500);
            }
        });

        // Handle dynamic content updates
        this.bindDynamicContentHandlers();
    }

    /**
     * Submit form via AJAX
     */
    async submitForm(form) {
        const url = form.action;
        const method = form.method || 'POST';
        const formData = new FormData(form);
        const btn = form.querySelector('button[type="submit"]');
        // Show loading state
        this.setLoadingState(btn, true);

        // CRITICAL: Add files from the file manager
        if (window.fileManagers && window.fileManagers['ajax-modal']) {
            const fileManager = window.fileManagers['ajax-modal'];
            const files = fileManager.getFiles();

            if (files && files.length > 0) {
                files.forEach((file, index) => {
                    // Append each file with the correct name
                    formData.append('attachments[]', file, file.name);
                });
            }
        }

        try {
            const response = await this.request(method, url, formData, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            await this.handleResponse(response, form);

        } catch (error) {
            console.log(error);
            this.showError('Network error occurred. Please try again.');
        } finally {
            this.setLoadingState(btn, false);
        }
    }

    /**
     * Handle AJAX link click
     */
    async handleAjaxLink(link) {
        const url = link.href;
        const method = link.dataset.method || 'GET';
        const confirmMsg = link.dataset.confirm;
        const data = link.dataset.data;

        if (confirmMsg && !confirm(confirmMsg)) {
            return;
        }

        this.setLoadingState(link, true);

        try {
            const response = await this.request(method, url, data);
            await this.handleResponse(response, link);
        } catch (error) {
            console.log(error);
            this.showError('Action failed. Please try again.');
        } finally {
            this.setLoadingState(link, false);
        }
    }

    /**
     * Generic request method
     */
    async request(method, url, data = null, options = {}) {
        const config = {
            method: method.toUpperCase(),
            headers: { ...this.defaultHeaders, ...options.headers },
            ...options
        };

        if (data && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(config.method)) {
            if (data instanceof FormData) {
                delete config.headers['Content-Type'];
                config.body = data;
            } else {
                data = JSON.parse(data);
                config.headers['Content-Type'] = 'application/x-www-form-urlencoded';
                const params = new URLSearchParams();

                // Simple flattening (for flat objects)
                Object.keys(data).forEach(key => {
                    params.append(key, data[key]);
                });

                config.body = params.toString();
            }
            console.log(config)
        }

        const response = await fetch(url, config);

        // Handle session expired
        if (response.status === 401) {
            this.handleSessionExpired();
            throw new Error('Session expired');
        }

        // Handle permission denied
        if (response.status === 403) {
            this.showError('You do not have permission to perform this action.');
            throw new Error('Permission denied');
        }

        const contentType = response.headers.get('content-type');

        if (contentType && contentType.includes('application/json')) {
            const result = await response.json();

            // Update CSRF token if provided
            if (result.csrf_token) {
                this.updateCsrfToken(result.csrf_token);
            }

            return result;
        }

        return await response.text();
    }

    /**
     * Handle AJAX response
     */
    async handleResponse(response, element = null) {
        if (response.status === 'success') {
            await this.handleSuccess(response, element);
        } else if (response.status === 'error') {
            this.handleError(response, element);
        }

        // Handle content updates
        if (response.data?.update) {
            this.updateContent(response.data.update);
        }

        // Handle redirect
        if (response.data?.redirect) {
            setTimeout(() => {
                window.location.href = response.data.redirect;
            }, response.data.redirect_delay || 1000);
        }

        // Handle modal close
        if (response.data?.close_modal) {
            this.closeModal();
        }
    }

    /**
     * Handle successful response
     */
    async handleSuccess(response, element) {
        // Show success message
        if (response.message) {
            this.showSuccess(response.message);
        }

        // Handle modal content
        if (response.data?.modal_content) {
            this.updateModalContent(response.data.modal_content);
        }

        // Handle form reset
        if (element && element.tagName === 'FORM' && response.data?.reset_form !== false) {
            element.reset();
        }

        // Handle table reload
        if (response.data?.reload_table) {
            this.reloadDataTable(response.data.reload_table);
        }

        if (response.data?.remove_row_task) {
            document.querySelectorAll('.' + response.data.remove_row_task)
                .forEach(el => el.remove());
        }

        // Handle page reload
        if (response.data?.reload_page) {
            setTimeout(() => location.reload(), 1000);
        }

        // Execute custom callback
        if (response.data?.callback && typeof window[response.data.callback] === 'function') {
            console.log("callback")
            window[response.data.callback](response.data);
        }
    }

    triggerModalPopup(trigger_key) {
        const wrapper = document.querySelector('.' + trigger_key);
        if (!wrapper) {
            return;
        }
        else {
            setTimeout(function () {
                if (wrapper.classList.contains('modal-trigger')) {
                    wrapper.click();
                }
            }, 500);
        }
    }

    /**
     * Handle error response
     */
    handleError(response, element) {
        // Show error message

        // Show validation errors
        // if (response.errors) {
        //     this.showValidationErrors(response.errors, element);
        // }

        if (response.data.errors) {
            this.showValidationErrors(response.data.errors, element);
        }
        else {
            if (response.message) {
                this.showError(response.message);
            }
        }

        // Highlight form fields with errors
        if (element && element.tagName === 'FORM' && response.errors) {
            this.highlightFormErrors(element, response.errors);
        }
    }

    /**
     * Open modal with AJAX content
     */
    async openModal(url, data = {}, to_init_select2 = false, elementToInit = '') {

        const param = {
            TITLE: data.title ?? 'Modal',
            SIZE: data.size ?? 'modal-md'
        }
        // Create modal if it doesn't exist
        if (!document.getElementById('ajax-modal')) {
            this.createModal(param);
        }

        const modal = document.getElementById('ajax-modal');
        const modalContent = document.getElementById('ajax-modal-content');
        const modalTitle = document.getElementById('ajax-modal-title');

        modalTitle.innerText = param.TITLE;
        const dialog = modal.querySelector('.modal-dialog');
        dialog.classList.remove(...dialog.classList);
        dialog.classList.add('modal-dialog', 'modal-dialog-centered', param.SIZE);

        // Show loading
        modalContent.innerHTML = `
            <div class="text-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading...</p>
            </div>
        `;

        // Show modal
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();

        //initiate multiple file upload
        window.initializeFileUpload('ajax-modal');

        try {
            // Load modal content
            const response = await this.request('GET', url, null, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Modal-Request': 'true'
                }
            });

            if (response.status === 'success' && response.data?.html) {
                modalContent.innerHTML = response.data.html;

                // handle response
                await this.handleResponse(response);

                // Initialize form validation
                this.initModalForms();

                // Focus first input
                setTimeout(() => {
                    const firstInput = modalContent.querySelector('input, select, textarea');
                    if (firstInput) firstInput.focus();
                    if (to_init_select2) {
                        this.initSelect2Element('#' + elementToInit);
                    }
                }, 100);

                // New dynamic select2 init for multiple select2 in modal
                this.initSelect2()

                // New dynamic select2 tags init for multiple select2 tags in modal
                this.initSelect2Tags()


            } else {
                throw new Error('Failed to load modal content');
            }

        } catch (error) {
            console.log(error);
            modalContent.innerHTML = `
                <div class="alert alert-danger m-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Failed to load content. Please try again.
                </div>
            `;
        }
    }

    /**
     * @param {*} elementToInit 
     * Reusable initselect2 element
     */
    initSelect2Element(elementToInit) {
        var placeholder = $(elementToInit).data('placeholder') ?? 'Select option';
        var multilabel = $(elementToInit).data('multilabel')

        if (multilabel == true) {

            $(elementToInit).select2({
                dropdownParent: $('.modal'),
                theme: 'bootstrap-5',
                placeholder: placeholder,
                allowClear: true,
                closeOnSelect: false,
                templateResult: function (data) {

                    if (data.loading) return data.text;

                    if (data.id && data.text && data.text.includes('@')) {

                        var parts = data.text.split(' - ');
                        return $(
                            '<div>' +
                            '<div class="employee-name-line">' + parts[0] + '</div>' +
                            '<div class="employee-email-line">' + parts[1] + '</div>' +
                            '</div>'
                        );
                    }
                    return data.text;
                },

                // Show only name when selected (still single line)
                templateSelection: function (data) {
                    if (data.id && data.text && data.text.includes('@')) {
                        var parts = data.text.split(' - ');
                        return parts[0];
                    }
                    return data.text;
                },
            });
        } else {
            let dropdownParent = $(elementToInit).closest('.modal').length ? $('.modal') : $(document.body);
            let closeOnSelect = $(elementToInit).attr('multiple') ? false : true;
            $(elementToInit).select2({
                dropdownParent: dropdownParent,
                theme: 'bootstrap-5',
                placeholder: placeholder,
                allowClear: true,
                closeOnSelect: closeOnSelect
            });
        }


    }

    /**
     * @param {*} elementToInit 
     * Reusable initselect2 element
     */
    initSelect2() {

        $('.modal').find('.select2').each((index, elementToInit) => {
            this.initSelect2Element(elementToInit);
        });
    }

    /**
     * @param {*} elementToInit 
     * Reusable initselect2 element
     */
    initSelect2Tags() {
        $('.select2-tags').each((index, elementToInit) => {

            var placeholder = $(elementToInit).data('placeholder') ?? 'Select option';
            var validate = $(elementToInit).data('validate') ?? null;

            $(elementToInit).select2({
                dropdownParent: $('.modal'),
                theme: 'bootstrap-5',
                tags: true,
                tokenSeparators: [',', ' '],
                selectOnClose: true,
                createTag: function (params) {
                    const tagValue = params.term.trim();

                    // Validate email format
                    if (validate == 'email') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(tagValue)) {
                            // Don't create invalid tag
                            if (/\s/g.test(emailRegex)) {
                                $('.select2-search__field').val('');
                            }
                            return null;
                        }

                    }

                    return {
                        id: tagValue,
                        text: tagValue,
                        newTag: true
                    };
                },
                placeholder: placeholder
            });
        });
    }

    /**
     * Create reusable modal
     */
    createModal(param) {
        const modalHTML = `
        <div class="modal fade" id="ajax-modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered ${param.SIZE}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ajax-modal-title">${param.TITLE}</h5> 
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            
                    </div>
                    <div class="modal-body" id="ajax-modal-content">
                        <!-- Content loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Close modal
     */
    closeModal() {
        const modal = document.getElementById('ajax-modal');
        if (modal) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }

            //clear selected files
            if (window.fileManagers && window.fileManagers['ajax-modal']) {
                const fileManager = window.fileManagers['ajax-modal'];
                fileManager.clearFiles()
            }
        }
    }

    /**
     * Update modal content dynamically
     */
    updateModalContent(html) {
        const modalContent = document.getElementById('ajax-modal-content');
        if (modalContent) {
            modalContent.innerHTML = html;
            this.initModalForms();
        }
    }

    /**
     * Initialize forms inside modal
     */
    initModalForms() {
        const modalContent = document.getElementById('ajax-modal-content');
        if (modalContent) {
            // Make all forms in modal AJAX forms
            modalContent.querySelectorAll('form').forEach(form => {
                form.classList.add('ajax-form');
            });
        }
    }

    /**
     * Show success notification
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Show error notification
     */
    showError(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const alertClass = {
            success: 'alert-success',
            error: 'alert-danger',
            warning: 'alert-warning',
            info: 'alert-info'
        }[type];

        const icon = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        }[type];

        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show`;
        notification.role = 'alert';
        notification.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        container.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    /**
     * Show validation errors
     */
    showValidationErrors(errors, element) {
        let errorMessage = '';

        if (typeof errors === 'object') {
            errorMessage = Object.values(errors).join('<br>');
        } else if (Array.isArray(errors)) {
            errorMessage = errors.join('<br>');
        } else {
            errorMessage = errors;
        }

        this.showError(errorMessage);
    }

    /**
     * Highlight form fields with errors
     */
    highlightFormErrors(form, errors) {
        // Remove previous error highlights
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });

        // Add new error highlights
        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');

                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = errors[fieldName];

                field.parentNode.appendChild(feedback);
            }
        });
    }

    /**
     * Set loading state for element
     */
    setLoadingState(element, isLoading) {

        if (!element) {
            return;
        }

        if (isLoading) {
            // element.setAttribute('data-original-html', element.innerHTML);
            // element.innerHTML = `
            //     <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            //     Loading...
            // `;
            //element.disabled = true;
        } else {
            // const originalHtml = element.getAttribute('data-original-html');
            // if (originalHtml) {
            //     element.innerHTML = originalHtml;
            //     element.removeAttribute('data-original-html');
            // }
            //element.disabled = false;
        }
    }

    /**
     * Update content dynamically
     */
    updateContent(updates) {
        Object.keys(updates).forEach(selector => {
            const elements = document.querySelectorAll(selector);
            const content = updates[selector];
            const action = content.action || 'replace';
            const html = content.html || content;
            elements.forEach(element => {
                switch (action) {
                    case 'append':
                        element.insertAdjacentHTML('beforeend', html);
                        break;
                    case 'prepend':
                        element.insertAdjacentHTML('afterbegin', html);
                        break;
                    case 'before':
                        element.insertAdjacentHTML('beforebegin', html);
                        break;
                    case 'after':
                        element.insertAdjacentHTML('afterend', html);
                        break;
                    case 'remove':
                        element.remove();
                        break;
                    case 'replace-with':
                        element.outerHTML = html;
                        break;
                    case 'replace':
                        element.innerHTML = html;
                        break;
                    case 'removeClass':
                        element.classList.remove(html);
                        break;
                    case 'addClass':
                        element.classList.add(html);
                        break;
                    case 'show':
                        element.style.display = 'block';
                        break;
                    case 'hide':
                        element.style.display = 'none';
                        break;
                    default:
                        element.innerHTML = html;
                        break;
                }
            });
        });

        // Rebind handlers for new content
        this.bindDynamicContentHandlers();
    }

    /**
     * Bind handlers for dynamic content
     */
    bindDynamicContentHandlers() {
        // Rebind AJAX forms in new content
        document.querySelectorAll('.ajax-form:not([data-bound])').forEach(form => {
            form.setAttribute('data-bound', 'true');
        });

        // Rebind AJAX links in new content
        document.querySelectorAll('.ajax-link:not([data-bound])').forEach(link => {
            link.setAttribute('data-bound', 'true');
        });
    }

    /**
     * Reload DataTable
     */
    reloadDataTable(tableId) {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            table.ajax.reload(null, false);
        }
    }

    /**
     * Update CSRF token
     */
    updateCsrfToken(token) {
        this.csrfToken = token;
        this.defaultHeaders[this.csrfHeader] = token;

        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', token);
        }

        // Update all forms
        document.querySelectorAll('form').forEach(form => {
            const csrfInput = form.querySelector('input[name="csrf_token"]');
            if (csrfInput) {
                csrfInput.value = token;
            }
        });
    }

    /**
     * Handle session expired
     */
    handleSessionExpired() {
        this.showError('Your session has expired. Please login again.');
        setTimeout(() => {
            window.location.href = '/login';
        }, 2000);
    }
}

// Create global instance
const Tihor = new AjaxLibrary();
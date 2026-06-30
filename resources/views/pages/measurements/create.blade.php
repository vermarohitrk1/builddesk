<form action="{{ route('measurements.store') }}" method="POST" class="ajax-form">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
    
    <div class="mb-3">
        <label class="form-label">Measurement Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" required placeholder="e.g. Ground Floor survey, Initial Visit">
    </div>

    <div class="mb-3">
        <label class="form-label">General Visit Notes</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="Any general observations..."></textarea>
    </div>

    <hr>
    <div class="mb-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Measurement Items (Notes)</h6>
        <button type="button" class="btn btn-sm btn-outline-primary add-item-row" data-url="{{ route('measurements.add-item-row') }}">
            <i class="fas fa-plus me-1"></i> Add Item
        </button>
    </div>

    <div id="items-container">
        <!-- Default Row -->
        @include('pages.measurements.components.item')
    </div>

    <!-- Images (multi upload) -->
    <div class="mb-3">            
        <!-- Hidden file input -->
        <input type="file" 
            class="d-none" 
            name="attachments[]" 
            id="attachments" 
            multiple
            accept="*/*">
        
        <!-- Add File Button -->
        <div class="mb-3 d-flex justify-content-between">
            <label class="form-label">Attachments</label>
            <button type="button" class="btn btn-outline-primary" id="addFilesBtn">
                <i class="fas fa-plus me-2"></i> Add Files
            </button>
        </div>
        
        <!-- Files List Container -->
        <div class="files-list-container border rounded p-3" id="filesListContainer" style="min-height: 60px;">
            <div class="empty-state text-center text-muted py-4" id="emptyState">
                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                <p class="mb-0">No files selected</p>
            </div>
            
            <!-- Files will be appended here -->
            <div class="files-list" id="filesList"></div>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Measurement</button>
    </div>
</form>

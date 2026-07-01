<div class="modal-header">
    <h5 class="modal-title">Add Expense Category</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form action="{{ route('expenses.categories.store') }}" method="POST" class="ajax-form">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Travel, Utilities..." required>
        </div>
        <div class="text-end mt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Category</button>
        </div>
    </form>
</div>

<form action="{{ route('measurements.update', $measurement->id) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label class="form-label">Measurement Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" required value="{{ $measurement->title }}" placeholder="e.g. Ground Floor survey, Initial Visit">
    </div>

    <div class="mb-3">
        <label class="form-label">General Visit Notes</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="Any general observations...">{{ $measurement->notes }}</textarea>
    </div>

    <hr>
    <div class="mb-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Measurement Items (Notes)</h6>
        <button type="button" class="btn btn-sm btn-outline-primary add-item-row" data-url="{{ route('measurements.add-item-row') }}">
            <i class="fas fa-plus me-1"></i> Add Item
        </button>
    </div>

    <div id="items-container">
        @forelse($measurement->items as $item)
            <div class="measurement-item item-row border p-2 mb-2 bg-light position-relative">
                <input type="hidden" name="items[]" value="">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="item_title[]" class="form-control form-control-sm" placeholder="Title (e.g. Room 1 Window)" required value="{{ $item->title }}">
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="item_description[]" class="form-control form-control-sm" placeholder="Notes (e.g. H=12, W=4, 2 Track )" required value="{{ $item->description }}">
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-sm btn-link text-danger remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            @include('pages.measurements.components.item')
        @endforelse
    </div>

    <div class="text-end mt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Measurement</button>
    </div>
</form>

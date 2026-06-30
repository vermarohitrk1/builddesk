
<div class="measurement-item item-row border p-2 mb-2 bg-light">
    <input type="hidden" name="items[]" value="">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" name="item_title[]" class="form-control form-control-sm" placeholder="Title (e.g. Room 1 Window)" required>
        </div>
        <div class="col-md-7">
            <input type="text" name="item_description[]" class="form-control form-control-sm" placeholder="Notes (e.g. H=12, W=4, 2 Track )" required>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-sm btn-link text-danger remove-row" style="display:none;">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</div>
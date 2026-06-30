<form action="{{ route('followups.update', $followup->id) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label class="form-label">Follow-up Datetime <span class="text-danger">*</span></label>
        <input type="datetime-local" name="followup_date" class="form-control" required value="{{ $followup->followup_date->format('Y-m-d H:i') }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Follow-up Note <span class="text-danger">*</span></label>
        <textarea name="note" class="form-control" rows="4" placeholder="Describe the action/next steps to be taken..." required>{{ $followup->note }}</textarea>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Follow-up</button>
    </div>
</form>

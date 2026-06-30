<form action="{{ route('followups.store') }}" method="POST" class="ajax-form">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
    
    <div class="mb-3">
        <label class="form-label">Follow-up Date <span class="text-danger">*</span></label>
        <input type="datetime-local" name="followup_date" class="form-control" required value="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Follow-up Note <span class="text-danger">*</span></label>
        <textarea name="note" class="form-control" rows="4" placeholder="Describe the action/next steps to be taken..." required></textarea>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Follow-up</button>
    </div>
</form>

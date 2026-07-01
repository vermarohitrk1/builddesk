<form action="{{ route('customers.update', $customer->id) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required value="{{ $customer->name }}">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="3">{{ $customer->address }}</textarea>
    </div>

    <div class="text-end border-top pt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Customer</button>
    </div>
</form>

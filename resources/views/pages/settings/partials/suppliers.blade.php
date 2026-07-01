<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Suppliers</h5>
        <button class="btn btn-primary btn-sm modal-trigger"
            data-url="{{ route('suppliers.create') }}"
            data-title="Add Supplier">
            <i class="fas fa-plus me-1"></i> Add Supplier
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="suppliers-table" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>GST Number</th>
                        <th>Created At</th>
                        <th width="80px" class="text-end">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    var suppliersTable;
    $(function() {
        suppliersTable = $('#suppliers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('suppliers.data') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'mobile', name: 'mobile', defaultContent: '<span class="text-muted">—</span>' },
                { data: 'gst_number', name: 'gst_number', defaultContent: '<span class="text-muted">—</span>' },
                { data: 'created_at', name: 'created_at', render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : '';
                }},
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search suppliers..."
            }
        });
    });
</script>

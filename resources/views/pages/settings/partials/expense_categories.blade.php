<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Expense Categories</h5>
        <button class="btn btn-primary btn-sm" onclick="createExpenseCategory()">
            <i class="fas fa-plus me-1"></i> Add Category
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="expense-categories-table" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th>Category Name</th>
                        <th>Created At</th>
                        <th width="100px" class="text-end">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    var expenseCategoryTable;
    $(function() {
        expenseCategoryTable = $('#expense-categories-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('expenses.categories.data') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'created_at', name: 'created_at', render: function(data) {
                    return data ? new Date(data).toLocaleDateString() : '';
                }},
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search categories..."
            }
        });
    });

    function createExpenseCategory() {
        $.get("{{ route('expenses.categories.create') }}", function(res) {
            $('#ajax-modal .modal-content').html(res.html);
            $('#ajax-modal').modal('show');
        });
    }

    function editExpenseCategory(id) {
        $.get("/expenses/categories/" + id + "/edit", function(res) {
            $('#ajax-modal .modal-content').html(res.html);
            $('#ajax-modal').modal('show');
        });
    }

    function deleteExpenseCategory(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This category won't be deleted if it is linked to active expenses.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/expenses/categories/" + id,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if(res.status == 'success') {
                            toastr.success(res.message);
                            expenseCategoryTable.ajax.reload();
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Error occurred deleting record.');
                    }
                });
            }
        });
    }
</script>

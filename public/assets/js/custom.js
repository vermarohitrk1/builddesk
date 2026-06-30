
$(document).ready(function () {
    let checkTimeout = null;

    $(document).on('input', '#contact_mobile', function () {
        const mobile = $(this).val();
        const resultDiv = $('#mobile-check-result');

        clearTimeout(checkTimeout);

        if (mobile.length < 10) {
            resultDiv.html('');
            return;
        }

        checkTimeout = setTimeout(() => {
            var url = BASE_URL + 'leads/check-mobile';
            $.get(url, { mobile: mobile }, function (response) {
                if (response.status === 'exists') {
                    let html = `<div class="alert alert-warning py-2 mb-0 mt-2" style="font-size: 0.85rem;">
                        <i class="fas fa-exclamation-triangle me-2"></i> ${response.message}<br>
                        <div class="mt-2">`;

                    if (response.lead_id) {
                        html += `<a href="#" class="btn btn-xs btn-dark me-2">Open Existing Lead</a>`;
                    }
                    html += `<span class="text-muted">You can still create a "New Opportunity" by proceeding.</span></div></div>`;
                    resultDiv.html(html);
                } else {
                    resultDiv.html('<small class="text-success"><i class="fas fa-check-circle"></i> New Mobile Number</small>');
                }
            });
        }, 500);
    });
});


$(document).on('change', '.dynamic-select', function () {
    const $this = $(this);
    const value = $this.val();

    const targetSelector = $this.data('target');
    const urlTemplate = $this.data('url');
    const placeholder = $this.data('placeholder') || 'Select';

    const $target = $(targetSelector);

    // // Reset target if no value selected
    if (!value) {
        // $target.html(`<option value="">${placeholder}</option>`);
        // $target.trigger('change.select2');
        // return;
    }

    const url = BASE_URL + urlTemplate.replace(':id', value);

    $.getJSON(url, function (response) {
        const list = response.data || [];

        $target.empty().append(`<option value="">${placeholder}</option>`);

        list.forEach(item => {
            $target.append(
                `<option value="${item.id}">${item.name}</option>`
            );
        });

        if ($target.hasClass('select2-hidden-accessible')) {
            $target.trigger('change.select2');
        }

        // Trigger generic change event for validation and other handlers
        $target.trigger('change');
    });
});


$(document).on('click', '.add-item-row', function () {
    const $container = $('#items-container');
    const url = $(this).data('url');

    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $container.append(response.data.html);
        }
    });

    // Show trash icons on all rows
    $container.find('.remove-row').show();
});

$(document).on('click', '.remove-row', function () {
    $(this).closest('.item-row').remove();
    calculateTotal();
    // Hide trash icons if only one row left
    if ($('#items-container').find('.item-row').length <= 1) {
        $('#items-container').find('.remove-row').hide();
    }
});


$(document).on('input', '.q-amount, #discount_value', function () {
    calculateTotal();
});

$(document).on('change', '#discount_type', function () {
    calculateTotal();
});

function calculateTotal() {
    let subTotal = 0;
    $('.q-amount').each(function () {
        const val = parseFloat($(this).val());
        if (!isNaN(val)) subTotal += val;
    });
    $('#sub-total').text('₹ ' + subTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    $('#sub_total_val').val(subTotal);

    let discountValue = parseFloat($('#discount_value').val()) || 0;
    let discountType = $('#discount_type').val();
    let discountAmount = 0;

    if (discountType === 'percentage') {
        discountAmount = (subTotal * discountValue) / 100;
    } else {
        discountAmount = discountValue;
    }

    let grandTotal = subTotal - discountAmount;
    if (grandTotal < 0) grandTotal = 0;

    $('#discount-amount-display').text('₹ ' + discountAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    $('#discount_amount_val').val(discountAmount);

    $('#grand-total').text('₹ ' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    $('#total_amount_val').val(grandTotal);
}

// ─── Measurement Suggestions for Quotation Items ─────────────────────────────

// Open suggestions when the lightbulb button is clicked
$(document).on('click', '.suggest-btn', function (e) {
    e.preventDefault();

    const $btn = $(this);
    const $item = $btn.closest('.quotation-item');
    const $panel = $item.find('.measurement-suggestions-panel');
    const $list = $panel.find('.suggestions-list');

    // Toggle: if already open, close it
    if (!$panel.hasClass('d-none')) {
        $panel.addClass('d-none');
        return;
    }

    // Close any other open panels first
    $('.measurement-suggestions-panel').addClass('d-none');

    // Show panel with loading state
    $list.html('<div class="text-center text-muted py-3 small"><i class="fas fa-spinner fa-spin me-1"></i> Loading...</div>');
    $panel.removeClass('d-none');

    $.ajax({
        url: BASE_URL + '/quotations/get-measurement-suggestions',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            lead_id: $('input[name="lead_id"]').val() || ''
        },
        success: function (response) {
            if (response && response.data && response.data.items && response.data.items.length > 0) {
                let html = '';
                response.data.items.forEach(function (item) {
                    html += `<button type="button" class="list-group-item list-group-item-action suggestion-item py-2 px-3" data-title="${item.title}" data-description="${item.description}">
                        <div class="fw-semibold small">${item.title}</div>
                        <div class="text-muted" style="font-size:0.78rem;">${item.description}</div>
                    </button>`;
                });
                $list.html(html);
            } else {
                $list.html('<div class="text-center text-muted py-3 small">No measurement notes found for this lead.</div>');
            }
        },
        error: function (e) {
            console.log(e);
            $list.html('<div class="text-center text-danger py-3 small">Failed to load suggestions.</div>');
        }
    });
});

// Click a suggestion → fill the description input and close panel
$(document).on('click', '.suggestion-item', function (e) {
    e.preventDefault();
    const $item = $(this).closest('.quotation-item');
    const title = $(this).data('title');
    const description = $(this).data('description');
    // Fill description with "Title: Description" or just the title
    $item.find('input[name="item_description[]"]').val(title + ': ' + description);
    $item.find('.measurement-suggestions-panel').addClass('d-none');
});

// Close button inside panel
$(document).on('click', '.close-suggestions', function (e) {
    e.preventDefault();
    $(this).closest('.measurement-suggestions-panel').addClass('d-none');
});

// Close panel when clicking outside
$(document).on('click', function (e) {
    if (!$(e.target).closest('.quotation-item').length) {
        $('.measurement-suggestions-panel').addClass('d-none');
    }
});



// ----------------- Supplier Modal -----------------

$(document).ready(function () {

    $(document).on('change', '#expense-supplier-select', function () {

        // const supplierModalEl = document.getElementById('supplierModal');
        // let supplierModal;

        // // Avoid double instantiation issue if script re-runs
        // if (supplierModalEl) {
        //     supplierModal = new bootstrap.Modal(supplierModalEl);
        // }

        if ($(this).val() === 'add_new_supplier') {
            // Reset selection to default empty
            $(this).val('');

            // Clear errors and input fields inside supplier form
            $('#supplier-errors').addClass('d-none').html('');
            $('#inline-supplier-form')[0].reset();

            // Show modal
            // if (supplierModal) {
            //     supplierModal.show();
            // }
            $('#supplierModal').modal('show');
        }
    });

    $(document).on('submit', '#inline-supplier-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $submitBtn = $('#save-inline-supplier-btn');
        $submitBtn.prop('disabled', true).text('Saving...');
        $('#supplier-errors').addClass('d-none').html('');

        $.ajax({
            url: BASE_URL + "/suppliers",
            method: "POST",
            data: $form.serialize(),
            success: function (response) {
                $submitBtn.prop('disabled', false).text('Save Supplier');

                if (response.status === 'success') {
                    const newSupplier = response.data.supplier;

                    // Create new option and insert it right after the Add New Supplier option
                    const newOption = $('<option>', {
                        value: newSupplier.id,
                        text: newSupplier.name,
                        selected: true
                    });

                    $('#expense-supplier-select option[value="add_new_supplier"]').after(newOption);

                    // Close the modal
                    $('#supplierModal').modal('hide');

                    // Success toast if Toastr is available, or alert
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Supplier added successfully!');
                    }
                } else {
                    $('#supplier-errors').removeClass('d-none').html(response.message || 'Validation failed.');
                }
            },
            error: function (xhr) {
                $submitBtn.prop('disabled', false).text('Save Supplier');
                let errMsg = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errMsg = '';
                    Object.keys(errors).forEach(key => {
                        errMsg += `<div>${errors[key][0]}</div>`;
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                $('#supplier-errors').removeClass('d-none').html(errMsg);
            }
        });
    });
});
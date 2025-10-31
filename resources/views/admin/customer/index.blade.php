@extends('admin.layouts.app')
@section('style')
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Ferm</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Ferm
            </button>

            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                Import Excel
            </button>

            <!-- ✅ Download Sample Button -->
            <a href="{{ asset('sample/customers_sample.xlsx') }}" class="btn btn-outline-secondary">
                Download Sample
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="customerTable">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Firm Name</th>
                                    <th>Person Name</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Ferm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id">
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="mb-3">
                        <label for="firm_name" class="form-label">Firm Name</label>
                        <input type="text" id="firm_name" name="firm_name" class="form-control" placeholder="Enter Firm Name">
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="mb-3">
                        <label for="person_name" class="form-label">Person Name</label>
                        <input type="text" id="person_name" name="person_name" class="form-control" placeholder="Enter Person Name">
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" class="form-control" placeholder="Enter Contact Number">
                        <small class="error-text text-danger"></small>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email">
                        <small class="error-text text-danger"></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="AddCustomerBtn">Save</button>
            </div>
        </div>
    </div>
</div>


<!-- Import Excel Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Ferm from Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="importExcelForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Select Excel File (.xlsx, .xls)</label>
                        <input type="file" id="import_file" name="import_file" class="form-control" accept=".xlsx,.xls">
                        <small class="error-text text-danger"></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="ImportExcelBtn">Upload</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
$(document).ready(function() {

    const table = $('#customerTable').DataTable({
            processing: true,
            ajax: {
                url: "{{ route('admin.customer.getall') }}",
                type: 'GET'
            },
            columns: [
                {
                    data: 'logo',
                    render: (data) => {
                        if (data && data !== '') {
                            return `<img src="${data}" alt="Logo" width="50" height="50" class="rounded-circle border">`;
                        } else {
                            return `<img src="{{ asset('uploads/customers/noimg.jpg') }}" width="50" height="50" class="rounded-circle border">`;
                        }
                    }
                },
                { data: 'firm_name' },
                { data: 'person_name' },
                { data: 'contact_number' },
                { data: 'email' },
                {
                    data: 'status',
                    render: (data) => {
                        return data === "active"
                            ? '<span class="badge bg-label-success me-1">Active</span>'
                            : '<span class="badge bg-label-danger me-1">Inactive</span>';
                    }
                },
                {
                    data: 'id',
                    render: (data, type, row) => {
                        const statusBtn = row.status === 'inactive'
                            ? `<button type="button" class="btn btn-sm btn-success me-1" onclick="updateCustomerStatus(${row.id}, 'active')">Activate</button>`
                            : `<button type="button" class="btn btn-sm btn-warning me-1" onclick="updateCustomerStatus(${row.id}, 'inactive')">Deactivate</button>`;

                        return `
                            <button class="btn btn-sm btn-info me-1" onclick="editCustomer(${data})">Edit</button>
                            ${statusBtn}
                            <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${data})">Delete</button>
                        `;
                    }
                }
            ]
        });

    // Add / Update Customer
    $('#AddCustomerBtn').click(function() {
        let formData = new FormData($('#addCustomerForm')[0]);
        $('.error-text').text('');

        let id = $('#customer_id').val();
        let url = id ? "{{ route('admin.customer.update', ':id') }}".replace(':id', id) : "{{ route('admin.customer.store') }}";
        let method = id ? 'POST' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    Toast.fire({ icon: "success", title: response.message });
                    $('#addModal').modal('hide');
                    $('#addCustomerForm')[0].reset();
                    $('#modalTitle').text('Add Customer');
                    $('#customer_id').val('');
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`#${field}`).siblings('.error-text').text(errors[field][0]);
                    }
                } else {
                    Toast.fire({ icon: "error", title: "An unexpected error occurred." });
                }
            }
        });
    });

    // Edit Customer
    window.editCustomer = function(id) {
        const url = '{{ route("admin.customer.get", ":id") }}'.replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                let data = response.data;
                $('#modalTitle').text('Edit Customer');
                $('#customer_id').val(data.id);
                $('#firm_name').val(data.firm_name);
                $('#person_name').val(data.person_name);
                $('#contact_number').val(data.contact_number);
                $('#email').val(data.email);
                $('#addModal').modal('show');
            }
        });
    };

    // Change Status

    window.updateCustomerStatus = function(id, status) {
            const message = status === 'active'
                ? "This customer will be activated."
                : "This customer will be deactivated.";

            Swal.fire({
                title: "Are you sure?",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.customer.status") }}',
                        type: 'POST',
                        data: {
                            id: id,
                            status: status,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Toast.fire({ icon: "success", title: response.message });
                                // ✅ Reload table properly after success
                                $('#customerTable').DataTable().ajax.reload(null, false);
                            } else {
                                Toast.fire({ icon: "error", title: "Failed to update status" });
                            }
                        },
                        error: function() {
                            Toast.fire({ icon: "error", title: "Something went wrong" });
                        }
                    });
                }
            });
        };


    // Delete Customer
    window.deleteCustomer = function(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to delete this customer?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                const url = '{{ route("admin.customer.destroy", ":id") }}'.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({ icon: "success", title: response.message });
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
    };

    // Import Excel
    $('#ImportExcelBtn').click(function() {
        let formData = new FormData($('#importExcelForm')[0]);
        $('.error-text').text('');

        $.ajax({
            url: "{{ route('admin.customer.import') }}", // ✅ Backend route for import
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    Toast.fire({ icon: "success", title: response.message });
                    $('#importModal').modal('hide');
                    $('#importExcelForm')[0].reset();
                    $('#customerTable').DataTable().ajax.reload(null, false);
                } else {
                    Toast.fire({ icon: "error", title: response.message || 'Import failed!' });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`#${field}`).siblings('.error-text').text(errors[field][0]);
                    }
                } else {
                    Toast.fire({ icon: "error", title: "Something went wrong!" });
                }
            }
        });
    });
});
</script>
@endsection

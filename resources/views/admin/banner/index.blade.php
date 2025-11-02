@extends('admin.layouts.app')

@section('style')
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6 text-start">
            <h5 class="py-2 mb-2">
                <span class="text-primary fw-light">Banner Management</span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                Add Banner
            </button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="bannerTable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>WhatsApp Content</th>
                                    <th>Email Content</th>
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
                <h5 class="modal-title" id="modalTitle">Add Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addbannerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="banner_id" name="banner_id">

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <small class="error-text text-danger"></small>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Banner Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Banner Name">
                        <small class="error-text text-danger"></small>
                    </div>

                    <div class="mb-3">
                        <label for="whatsappcontent" class="form-label">WhatsApp Content</label>
                        <textarea id="whatsappcontent" name="whatsappcontent" class="form-control" rows="3" placeholder="Enter WhatsApp Content"></textarea>
                        <small class="error-text text-danger"></small>
                    </div>

                    <div class="mb-3">
                        <label for="emailcontant" class="form-label">Email Content</label>
                        <textarea id="emailcontant" name="emailcontant" class="form-control" rows="3" placeholder="Enter Email Content"></textarea>
                        <small class="error-text text-danger"></small>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="AddbannerBtn">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
$(document).ready(function() {

    const table = $('#bannerTable').DataTable({
        processing: true,
        ajax: {
            url: "{{ route('admin.banner.getall') }}",
            type: 'GET'
        },
        columns: [
            {
                data: 'image',
                render: (data) => {
                    if (data && data !== '') {
                        return `<img src="{{ asset('') }}${data}" alt="Image" width="50" height="50" class="rounded border">`;
                    } else {
                        return `<img src="{{ asset('uploads/banners/noimg.jpg') }}" width="50" height="50" class="rounded border">`;
                    }
                }
            },
            { data: 'name' },
            { data: 'whatsappcontent' },
            { data: 'emailcontant' },
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
                        ? `<button type="button" class="btn btn-sm btn-success me-1" onclick="updatebannerStatus(${row.id}, 'active')">Activate</button>`
                        : `<button type="button" class="btn btn-sm btn-warning me-1" onclick="updatebannerStatus(${row.id}, 'inactive')">Deactivate</button>`;

                    // Use an <a> tag to open the logo in a new tab
                    const baseLogoUrl = "{{ route('admin.banner.logo', ':id') }}"; 
                    const logoUrl = baseLogoUrl.replace(':id', row.id);

                    const logoBtn = row.image
                        ? `<a href="${logoUrl}" class="btn btn-sm btn-secondary me-1">Set Logo</a>`
                        : `<a href="javascript:void(0)" class="btn btn-sm btn-secondary me-1 disabled">No Logo</a>`;

                    return `
                        <button class="btn btn-sm btn-info me-1" onclick="editbanner(${data})">Edit</button>
                        ${statusBtn}
                        ${logoBtn}
                        <button class="btn btn-sm btn-danger" onclick="deletebanner(${data})">Delete</button>
                    `;
                }
            }
        ]
    });

    // Add / Update Banner
    $('#AddbannerBtn').click(function() {
        let formData = new FormData($('#addbannerForm')[0]);
        $('.error-text').text('');
        let id = $('#banner_id').val();

        let url = id
            ? "{{ route('admin.banner.update', ':id') }}".replace(':id', id)
            : "{{ route('admin.banner.store') }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    Toast.fire({ icon: "success", title: response.message });
                    $('#addModal').modal('hide');
                    $('#addbannerForm')[0].reset();
                    $('#banner_id').val('');
                    $('#modalTitle').text('Add Banner');
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

    // Edit Banner
    window.editbanner = function(id) {
        const url = '{{ route("admin.banner.get", ":id") }}'.replace(':id', id);
        $.get(url, function(response) {
            if (response.success) {
                let data = response.data;
                $('#modalTitle').text('Edit Banner');
                $('#banner_id').val(data.id);
                $('#name').val(data.name);
                $('#whatsappcontent').val(data.whatsappcontent);
                $('#emailcontant').val(data.emailcontant);
                $('#addModal').modal('show');
            }
        });
    };

    // Update Status
    window.updatebannerStatus = function(id, status) {
        Swal.fire({
            title: "Are you sure?",
            text: status == 1 ? "Activate this banner?" : "Deactivate this banner?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.banner.status") }}',
                    type: 'POST',
                    data: {
                        id: id,
                        status: status,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({ icon: "success", title: response.message });
                            table.ajax.reload(null, false);
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

    // Delete Banner
    window.deletebanner = function(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This banner will be deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                const url = '{{ route("admin.banner.destroy", ":id") }}'.replace(':id', id);
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
});
</script>
@endsection

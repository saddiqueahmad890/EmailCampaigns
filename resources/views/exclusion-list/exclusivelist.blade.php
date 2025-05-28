@extends('admin.master')
@section('title')
    {{ __('Exclusion List') }}
@endsection
@section('manage')
    {{ __('Manage') }}
@endsection
@section('compaign')
    {{ __('Exclusion List') }}
@endsection

@section('content')
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Dashboard</h3>
            <h6 class="op-7 mb-2">Manage email exclusion list</h6>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="#" class="btn btn-label-info btn-round me-2" data-bs-toggle="modal" data-bs-target="#csvUploadModal">
                <i class="fa-solid fa-file-arrow-up"></i> <!-- Upload icon -->
                {{ __('Upload Exclusion List CSV') }}
            </a>

            <a href="#" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#singleUploadModal">
                <i class="fa-solid fa-user-minus"></i> <!-- Exclusion icon -->
                {{ __('Add Exclusion') }}
            </a>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="table-responsive">
                            <table id="exclusionTable" class="table table-striped table-bordered border">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $key => $exclusion)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $exclusion->email }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input status-switch"
                                                        data-id="{{ $exclusion->id }}"
                                                        {{ $exclusion->status == '1' ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <!-- Edit Button -->

                                                    <button class="btn btn-info btn-sm edit-btn"
                                                        data-id="{{ $exclusion->id }}" data-email="{{ $exclusion->email }}"
                                                        data-bs-toggle="modal" data-bs-target="#editEmailModal">
                                                        Edit
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <button class="btn btn-danger btn-sm delete-btn"
                                                        data-id="{{ $exclusion->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#deleteConfirmModal">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No exclusions found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- CSV Upload Modal -->
    <div class="modal fade" id="csvUploadModal" tabindex="-1" aria-labelledby="csvUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="csvUploadModalLabel">Upload Exclusion List CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('exclusion-list.csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="file" name="exclusion_csv" class="form-control" accept=".csv" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Single Email Upload Modal -->
    <div class="modal fade" id="singleUploadModal" tabindex="-1" aria-labelledby="singleUploadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="singleUploadModalLabel">Add Exclusion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('exclusion-list.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="email">Enter Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Exclusion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Email Modal -->
    <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmailModalLabel">Edit Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEmailForm" method="POST" action="#">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit-email-id">
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Email</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Confirmation Modal -->
    <div class="modal fade" id="statusConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Status Change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="statusChangeMessage"></p>
                    <form id="statusUpdateForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" id="statusInput">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="statusUpdateForm" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>






    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this email?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <!-- Delete Form -->
                    <form id="deleteEmailForm" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let selectedSwitch; // Store the clicked switch element

            document.querySelectorAll(".status-switch").forEach((switchElement) => {
                switchElement.addEventListener("change", function() {
                    selectedSwitch = this;
                    let isChecked = this.checked;
                    let exclusionId = this.getAttribute("data-id");

                    let statusMessage = isChecked ?
                        "After activation, campaign emails will NOT be sent to this email ID." :
                        "After deactivation, campaign emails will be sent to this email ID.";

                    document.getElementById("statusChangeMessage").innerText = statusMessage;

                    // Update form action with the new status update route
                    let form = document.getElementById("statusUpdateForm");
                    form.setAttribute("action", `/exclusion-list/${exclusionId}/status`);

                    // Update status input value
                    document.getElementById("statusInput").value = isChecked ? "1" : "0";

                    // Show confirmation modal
                    let statusModal = new bootstrap.Modal(document.getElementById(
                        "statusConfirmModal"));
                    statusModal.show();
                });
            });
            // Handle Edit Modal
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let exclusionId = this.getAttribute('data-id');
                    let email = this.getAttribute('data-email');

                    // Populate form fields
                    document.getElementById('edit-email-id').value = exclusionId;
                    document.getElementById('edit-email').value = email;

                    // Update the form action with the correct ID
                    let form = document.getElementById('editEmailForm');
                    form.action = `/exclusion-list/${exclusionId}`;
                });
            });


            // Handle Delete Confirmation Modal
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let exclusionId = this.getAttribute('data-id');

                    // Update the form action with the correct ID
                    let form = document.getElementById('deleteEmailForm');
                    form.action = `/exclusion-list/${exclusionId}`;
                });
            });

            // Handle Delete (AJAX)
            document.getElementById("deleteConfirmBtn").addEventListener("click", function() {
                let exclusionId = this.getAttribute("data-id");

                fetch(`/exclusion-list/${exclusionId}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Email deleted successfully!");
                            location.reload();
                        } else {
                            alert("Failed to delete email.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    </script>
@endsection

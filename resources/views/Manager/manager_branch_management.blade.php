@extends('Layout.manager_layout')

@section('page-title', 'City Burgers POS | Branch Management')

@section('content')
    <div class="container-fluid bg-white" id="orderHistory" style="overflow-x: hidden;">
        <div class="return p-3 text-dark">
            <a href="{{ route('manager-dashboard.get') }}" class="text-dark"><i class="ph ph-arrow-left me-1"></i></a>
        </div>
        <div class="header-title" style="left:29%;">
            <div class="fs-6 fw-bold">Branch Management</div>
        </div>

        <div class="wrapper">
            <div class="searchBar">
                <input id="searchQueryInput" type="text" name="searchQueryInput" placeholder="Search" value="" />
                <button id="searchQuerySubmit" type="submit" name="searchQuerySubmit">
                    <!-- SVG Search Icon -->
                </button>
            </div>
        </div>
        <div class="mt-3 text-end">
            <div class="btn btn-dark btn-sm rounded-pill">
                <span class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createNewBranch">
                    <i class="ph ph-plus me-1"></i>New Branch
                </span>
            </div>
        </div>
        <div class="container">
            <div class="lead mb-2 mt-3 d-flex align-items-center justify-content-center"><i class="ph ph-users-three me-1"
                    style="font-size: 1.5rem;"></i>Branches
            </div>
            <div class="branch-grid py-3">
                @if ($branches->isNotEmpty())
                    @foreach ($branches as $branch)
                        <div class="branch-card shadow-sm" style="position: relative;">
                            <div style="position: absolute; top: 5px; right: 5px;">
                                <i class="fa-solid fa-trash text-danger delete-branch" style="font-size: 1.2rem;"
                                    data-id="{{ $branch->id }}"></i>
                            </div>
                            <div class="profile-picture mb-2">
                                <img src="{{ $branch->branch_image ? '/storage/branch_images/' . $branch->branch_image : asset('assets/images/location.png') }}"
                                    class="rounded-circle img-fluid" alt="Branch Image" width="80" height="80">
                            </div>
                            <div class="branch-info text-center">
                                <span class="fw-bold mb-1" style="font-size: 0.8rem;">{{ $branch->name }}</span>
                                <span class="text-secondary text-capitalize"
                                    style="font-size: 0.8rem;">{{ $branch->address }}</span>
                                <div class="btn btn-dark btn-sm d-flex justify-content-center mt-2">View Branch</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center text-secondary">
                        <p>No branches found.</p>
                    </div>
                @endif
            </div>
        </div>
        <!--------- CREATE NEW BRANCH MODAL ------------>
        <div class="modal fade" id="createNewBranch" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Create New Cashier</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="branchName" class="form-label fw-bold">Branch Name</label>
                                        <input type="text" name="name" id="branchName" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="branchAddress" class="form-label fw-bold">Full Address</label>
                                        <input type="text" name="address" id="branchAddress" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="createBranchButton" class="btn btn-success">Create</button>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for click events on trash icons
            document.querySelectorAll('.delete-branch').forEach((btn) => {
                btn.addEventListener('click', function() {
                    const branchId = this.getAttribute('data-id');

                    // Show confirmation using iziToast
                    iziToast.question({
                        timeout: false,
                        close: false,
                        overlay: true,
                        displayMode: 'once',
                        id: 'question',
                        zindex: 999,
                        title: 'Are you sure?',
                        message: 'Do you really want to delete this cashier account?',
                        position: 'center',
                        buttons: [
                            ['<button><b>YES</b></button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');

                                // Make the AJAX request to delete the cashier
                                deleteBranch(branchId);
                            }, true],
                            ['<button>NO</button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');
                            }]
                        ],
                        onClosing: function(instance, toast, closedBy) {
                            console.info('Closed by: ' + closedBy);
                        },
                        onClosed: function(instance, toast, closedBy) {
                            console.info('Closed by: ' + closedBy);
                        }
                    });
                });
            });

            // Function to delete a branch using AJAX
            function deleteBranch(branchId) {
                $.ajax({
                    url: '/manager/branch/delete/' + branchId,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}", // Include CSRF token for Laravel
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            iziToast.success({
                                title: 'Success',
                                message: response.message,
                                position: 'topCenter'
                            });
                            // Optionally, remove the cashier card from the UI
                            document.querySelector(`[data-id="${branchId}"]`).closest('.branch-card')
                                .remove();
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: response.message,
                                position: 'topCenter'
                            });
                        }
                    },
                    error: function() {
                        iziToast.error({
                            title: 'Error',
                            message: 'Failed to delete branch. Please try again.',
                            position: 'topCenter'
                        });
                    }
                });
            }
        });


        // Handle the Create Cashier button click
        document.getElementById('createBranchButton').addEventListener('click', function() {
            // Get form values
            const branchName = document.getElementById('branchName').value;
            const branchAddress = document.getElementById('branchAddress').value;

            // Validate the fields (optional, for basic client-side validation)
            if (!branchName || !branchAddress) {
                iziToast.error({
                    title: 'Error',
                    message: 'All fields are required!',
                });
                return;
            }

            // Send AJAX request to the server
            $.ajax({
                url: "{{ route('branch-management.post') }}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}", // Laravel CSRF token
                    "name": branchName,
                    "address": branchAddress,
                },
                success: function(response) {
                    if (response.status === 'success') {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topCenter'
                        });

                        // Optionally, append the new branch to the branch grid
                        const newBranchrHTML = `
                        <div class="branch-card shadow-sm" style="position: relative;">
                            <div style="position: absolute; top: 5px; right: 5px;">
                                <i class="fa-solid fa-trash text-danger delete-branch" style="font-size: 1.2rem;" data-id="${response.branch.id}"></i>
                            </div>
                            <div class="profile-picture mb-2">
                                <img src="{{ asset('assets/images/location.png') }}" class="rounded-circle img-fluid" alt="Branch Profile" width="80" height="80">
                            </div>
                            <div class="branch-info text-center">
                                <span class="fw-bold mb-1" style="font-size: 0.8rem;">${response.branch.name}</span>
                                <span class="text-secondary text-capitalize" style="font-size: 0.8rem;"> ${response.branch.address}</span>
                                <div class="btn btn-dark btn-sm d-flex justify-content-center mt-2">View Branch</div>
                            </div>
                        </div>
                    `;
                        document.querySelector('.branch-grid').insertAdjacentHTML(
                            'beforeend', newBranchrHTML);

                        // Clear the form fields
                        document.getElementById('branchName').value = '';
                        document.getElementById('branchAddress').value = '';


                        // Close the modal
                        $('#createNewBranch').modal('hide');
                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: response.message,
                            position: 'topCenter'
                        });
                    }
                },
                error: function() {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to create branch. Please try again.',
                        position: 'topCenter'
                    });
                }
            });
        });
    </script>

    </script>
@endsection

<?php $__env->startSection('page-title', 'City Burgers POS | Branch Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid bg-white" id="orderHistory" style="overflow-x: hidden;">
        <div class="return p-3 text-dark">
            <a href="<?php echo e(route('manager-dashboard.get')); ?>" class="text-dark"><i class="ph ph-arrow-left me-1"></i></a>
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
                <?php if($branches->isNotEmpty()): ?>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="branch-card shadow-sm" style="position: relative;">
                            <div style="position: absolute; top: 5px; right: 5px;">
                                <i class="fa-solid fa-trash text-danger delete-branch" style="font-size: 1.2rem;"
                                    data-id="<?php echo e($branch->id); ?>"></i>
                            </div>
                            <div class="profile-picture mb-2">
                                <img src="<?php echo e($branch->image ? '/storage/' . $branch->image : asset('assets/images/location.png')); ?>"
                                    class="rounded-circle img-fluid" alt="Branch Image" width="150" height="150">
                            </div>
                            <div class="branch-info text-center">
                                <span class="fw-bold mb-1" style="font-size: 0.8rem;"><?php echo e($branch->name); ?></span>
                                <span class="text-secondary text-capitalize"
                                    style="font-size: 0.8rem;"><?php echo e($branch->address); ?></span>
                                <a href="<?php echo e(route('branch-management.view', encrypt($branch->id))); ?>"
                                    class="btn btn-dark btn-sm d-flex justify-content-center mt-2">View
                                    Branch</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-secondary">
                        <p class="text-center my-4">No branches found.</p>
                    </div>
                <?php endif; ?>
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
                                <div class="col-lg-12 d-flex justify-content-center align-items-center">
                                    <div class="branch-image" style="position: relative;">
                                        <img src="<?php echo e(asset('assets/images/location.png')); ?>"
                                            class="img-fluid border border-1" width="150px" height="150px" alt="">
                                        <label for="branchImage" id="cameraIcon"
                                            style="right: -5%; bottom:0; background: red;"><i
                                                class="fa-solid fa-camera text-light"></i></label>
                                        <input type="file" id="branchImage" hidden>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form my-3">
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
                        "_token": "<?php echo e(csrf_token()); ?>", // Include CSRF token for Laravel
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
            const branchImage = document.getElementById('branchImage').files[0];
            const branchName = document.getElementById('branchName').value;
            const branchAddress = document.getElementById('branchAddress').value;
            console.log(branchImage)
            // Validate the fields (optional, for basic client-side validation)
            if (!branchImage || !branchName || !branchAddress) {
                iziToast.error({
                    title: 'Error',
                    message: 'All fields are required!',
                });
                return;
            }

            // File size and type validation
            const fileType = branchImage.type;
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5 MB in bytes

            if (branchImage.size > maxSize) {
                iziToast.error({
                    title: 'Invalid File Size',
                    message: 'The file size must be 5MB or less!',
                    position: 'topRight',
                });
                return;
            }

            if (!validTypes.includes(fileType)) {
                iziToast.error({
                    title: 'Invalid File Type',
                    message: 'Only JPEG, PNG, JPG, and WEBP formats are allowed!',
                    position: 'topRight',
                });
                return;
            }

            // Create a FormData object for file upload
            let formData = new FormData();
            formData.append("_token", "<?php echo e(csrf_token()); ?>"); // Laravel CSRF token
            formData.append("name", branchName);
            formData.append("address", branchAddress);
            formData.append("image", branchImage);

            // Send AJAX request to the server
            $.ajax({
                url: "<?php echo e(route('branch-management.post')); ?>",
                type: 'POST',
                data: formData,
                processData: false, // Required for file uploads
                contentType: false, // Required for file uploads
                success: function(response) {
                    if (response.status === 'success') {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight'
                        });

                        // Optionally, append the new branch to the branch grid
                        const newBranchHTML = `
                            <div class="branch-card shadow-sm" style="position: relative;">
                                <div style="position: absolute; top: 5px; right: 5px;">
                                    <i class="fa-solid fa-trash text-danger delete-branch" style="font-size: 1.2rem;" data-id="${response.branch.id}"></i>
                                </div>
                                <div class="profile-picture mb-2">
                                    <img src="${response.branch.image ? '/storage/' + response.branch.image : '/assets/images/location.png'}"
                                        class="rounded-circle img-fluid" alt="Branch Profile" width="80" height="80">
                                </div>
                                <div class="branch-info text-center">
                                    <span class="fw-bold mb-1" style="font-size: 0.8rem;">${response.branch.name}</span>
                                    <span class="text-secondary text-capitalize" style="font-size: 0.8rem;">${response.branch.address}</span>
                                    <a href="/branch-management/${response.branch.id}/view"
                                    class="btn btn-dark btn-sm d-flex justify-content-center mt-2">View Branch</a>
                                </div>
                            </div>
                        `;

                        document.querySelector('.branch-grid').insertAdjacentHTML(
                            'beforeend', newBranchHTML);

                        // Clear the form fields
                        document.getElementById('branchName').value = '';
                        document.getElementById('branchAddress').value = '';
                        document.querySelector('.branch-image img').src =
                            "<?php echo e(asset('assets/images/location.png')); ?>";

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

        document.getElementById('branchImage').addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const fileType = file.type;
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                const maxSize = 5 * 1024 * 1024; // 5 MB in bytes

                // Validate file size
                if (file.size > maxSize) {
                    iziToast.error({
                        title: 'Invalid File Size',
                        message: 'The file size must be 5MB or less!',
                        position: 'topRight',
                    });
                    this.value = ''; // Clear the file input
                    return;
                }

                // Validate file type
                if (!validTypes.includes(fileType)) {
                    iziToast.error({
                        title: 'Invalid File Type',
                        message: 'Only JPEG, PNG, JPG, and WEBP formats are allowed!',
                        position: 'topRight',
                    });
                    this.value = ''; // Clear the file input
                    return;
                }

                // Preview the selected image in the modal
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.branch-image img').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.manager_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Manager/manager_branch_management.blade.php ENDPATH**/ ?>
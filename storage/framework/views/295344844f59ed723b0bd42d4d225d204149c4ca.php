<?php $__env->startSection('page-title', 'City Burgers POS | Cashier Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid bg-white" id="orderHistory" style="overflow-x: hidden;">
        <div class="return p-3 text-dark">
            <a href="<?php echo e(route('manager-dashboard.get')); ?>" class="text-dark"><i class="ph ph-arrow-left me-1"></i></a>
        </div>
        <div class="header-title" style="left:29%;">
            <div class="fs-6 fw-bold">Cashier Management</div>
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
                <span class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createNewCashier">
                    <i class="ph ph-plus me-1"></i>New Cashier
                </span>
            </div>
        </div>
        <div class="container">
            <div class="lead mb-2 mt-3 d-flex align-items-center justify-content-center"><i class="ph ph-users-three me-1"
                    style="font-size: 1.5rem;"></i>Cashier Accounts
            </div>
            <div class="cashier-grid py-3">
                <?php if($cashierUsers->isNotEmpty()): ?>
                    <?php $__currentLoopData = $cashierUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cashier-card shadow-sm" style="position: relative;">
                            <div style="position: absolute; top: 5px; right: 5px;">
                                <i class="fa-solid fa-trash text-danger delete-cashier" style="font-size: 1.2rem;"
                                    data-id="<?php echo e($cashier->id); ?>"></i>
                            </div>
                            <div class="profile-picture mb-2">
                                <img src="<?php echo e($cashier->profile ? '/storage/profiles/' . $cashier->profile : asset('assets/images/user.jpg')); ?>"
                                    class="rounded-circle img-fluid" alt="Cashier Profile" width="80" height="80">
                            </div>
                            <div class="cashier-info text-center">
                                <span class="fw-bold mb-1" style="font-size: 0.8rem;"><?php echo e($cashier->name); ?></span>
                                <span class="text-secondary text-capitalize"
                                    style="font-size: 0.8rem;"><?php echo e($cashier->contact_number); ?> | <?php echo e($cashier->sex); ?></span>
                                <a href="<?php echo e(route('cashier.profile.view', encrypt($cashier->id))); ?>"
                                    class="btn btn-dark btn-sm d-flex justify-content-center mt-2">View
                                    Profile</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="col-12 text-center text-secondary">
                        <p>No cashiers found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!--------- CREATE NEW CASHIER MODAL ------------>
        <div class="modal fade" id="createNewCashier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <label for="email" class="form-label fw-bold">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="password" class="form-label fw-bold">Password</label>
                                        <input type="password" name="password" id="password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="fullName" class="form-label fw-bold">Fullname</label>
                                        <input type="text" name="name" id="fullName" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="contactNumber" class="form-label fw-bold">Contact Number</label>
                                        <input type="text" name="contact_number" id="contactNumber" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="sex" class="form-label fw-bold">Sex</label>
                                        <select name="sex" class="form-select" id="sex">
                                            <option value="" hidden>Select Sex</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="createCashierBtn" class="btn btn-success">Create</button>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for click events on trash icons
            document.querySelectorAll('.delete-cashier').forEach((btn) => {
                btn.addEventListener('click', function() {
                    const cashierId = this.getAttribute('data-id');

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
                                deleteCashier(cashierId);
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

            // Function to delete a cashier using AJAX
            function deleteCashier(cashierId) {
                $.ajax({
                    url: '/manager/cashier/delete/' + cashierId,
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
                            document.querySelector(`[data-id="${cashierId}"]`).closest('.cashier-card')
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
                            message: 'Failed to delete cashier. Please try again.',
                            position: 'topCenter'
                        });
                    }
                });
            }
        });


        // Handle the Create Cashier button click
        document.getElementById('createCashierBtn').addEventListener('click', function() {
            // Get form values
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const name = document.getElementById('fullName').value;
            const contactNumber = document.getElementById('contactNumber').value;
            const sex = document.getElementById('sex').value;

            // Validate the fields (optional, for basic client-side validation)
            if (!email || !password || !name || !contactNumber || !sex) {
                iziToast.error({
                    title: 'Error',
                    message: 'All fields are required!',
                });
                return;
            }

            // Send AJAX request to the server
            $.ajax({
                url: "<?php echo e(route('cashier.create')); ?>",
                type: 'POST',
                data: {
                    "_token": "<?php echo e(csrf_token()); ?>", // Laravel CSRF token
                    "email": email,
                    "password": password,
                    "name": name,
                    "contact_number": contactNumber,
                    "sex": sex
                },
                success: function(response) {
                    if (response.status === 'success') {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topCenter'
                        });

                        // Optionally, append the new cashier to the cashier grid
                        const newCashierHTML = `
                            <div class="cashier-card shadow-sm" style="position: relative;">
                                <div style="position: absolute; top: 5px; right: 5px;">
                                    <i class="fa-solid fa-trash text-danger delete-cashier" style="font-size: 1.2rem;" data-id="${response.cashier.id}"></i>
                                </div>
                                <div class="profile-picture mb-2">
                                    <img src="<?php echo e(asset('assets/images/user.jpg')); ?>" class="rounded-circle img-fluid" alt="Cashier Profile" width="80" height="80">
                                </div>
                                <div class="cashier-info text-center">
                                    <span class="fw-bold mb-1" style="font-size: 0.8rem;">${response.cashier.name}</span>
                                    <span class="text-secondary text-capitalize" style="font-size: 0.8rem;">${response.cashier.contact_number} | ${response.cashier.sex}</span>
                                    <div class="btn btn-dark btn-sm d-flex justify-content-center mt-2">View Profile</div>
                                </div>
                            </div>
                        `;
                        document.querySelector('.cashier-grid').insertAdjacentHTML(
                            'beforeend', newCashierHTML);

                        // Clear the form fields
                        document.getElementById('email').value = '';
                        document.getElementById('password').value = '';
                        document.getElementById('fullName').value = '';
                        document.getElementById('contactNumber').value = '';
                        document.getElementById('sex').value = 'male';

                        // Close the modal
                        $('#createNewCashier').modal('hide');
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
                        message: 'Failed to create cashier. Please try again.',
                        position: 'topCenter'
                    });
                }
            });
        });
    </script>

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.manager_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Manager/manager_cashier_management.blade.php ENDPATH**/ ?>
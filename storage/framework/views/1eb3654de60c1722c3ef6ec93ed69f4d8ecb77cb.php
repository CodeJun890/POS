<?php $__env->startSection('page-title', 'City Burgers POS | View Branch'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="return my-2 text-dark">
            <a href="<?php echo e(route('manager-dashboard.get')); ?>" class="text-dark"><i class="ph ph-arrow-circle-left me-1"></i></a>
            Back
        </div>
        <div class="d-flex justify-content-center align-items-center">
            <span class="fw-bold lead text-uppercase mb-3">View Branch</span>
        </div>
        <div class="branch-image-bg">
            <img class="rounded mb-3"
                src="<?php echo e($branch->image ? '/storage/' . $branch->image : asset('assets/images/location.png')); ?>"
                alt="">
            <div class="branch-name">
                <div class="lead fw-bold text-light"><?php echo e($branch->name); ?></div>
                <div class="fw-bold text-light text-center"><?php echo e($branch->address); ?></div>
            </div>
        </div>

        <div class="assigned-cashier mt-4">
            <div class="d-flex justify-content-center gap-2 align-items-center mb-3">
                <div class="btn btn-danger btn-sm rounded-pill">
                    <span class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#assignCashier">
                        <i class="ph ph-plus me-1"></i>Add Cashier
                    </span>
                </div>
                <a href="<?php echo e(route('manager-order-history.get', ['branch_id' => encrypt($branch->id)])); ?>"
                    class="btn btn-dark btn-sm rounded-pill">
                    <span class="d-flex align-items-center">
                        <i class="ph-clock-counter-clockwise me-1"></i>Order History
                    </span>
                </a>
            </div>
            <div class="d-flex justify-content-center align-items-center ">
                <div class="order-calculate text-center bg-light p-2 w-100 rounded-4 border border-1 border-secondary">
                    <div class="d-flex justify-content-around">
                        <div class="text-center">
                            <div class="fs-6 fw-bold text-uppercase">Total</div>
                            <p class="m-0 total-sales">&#8369; <?php echo e(number_format($totalSales, 2)); ?></p>
                        </div>

                        <div class="text-center">
                            <div class="fs-6 fw-bold text-uppercase">Profit</div>
                            <p class="m-0">&#8369; <?php echo e(number_format($totalProfit, 2)); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lead fw-bold text-center mt-3">Assigned Cashiers</div>
            <div class="cashier-grid py-3">
                <?php if($cashiers->isNotEmpty()): ?>
                    <?php $__currentLoopData = $cashiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cashier-card shadow-sm" style="position: relative;">
                            <div style="position: absolute; top: 5px; right: 5px;">
                                <i class="fa-solid fa-trash text-danger remove-assigned-cashier" style="font-size: 1.2rem;"
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
    </div>

    <!--------- ASSIGN CASHIER MODAL ------------>
    <form action="<?php echo e(route('branch-management.assign', encrypt($branch->id))); ?>" method="POST" class="modal fade"
        id="assignCashier" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <?php echo csrf_field(); ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Assign Cashier</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-12">
                                <div class="form mb-2">
                                    <label for="cashier" class="form-label fw-bold">Select Cashier to Assign</label>
                                    <select name="cashier_id" class="form-select" id="cashier">
                                        <option value="" hidden>Select Cashier</option>
                                        <?php $__currentLoopData = $allCashiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(encrypt($cashier->id)); ?>"><?php echo e($cashier->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="createCashierBtn" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for click events on trash icons
            document.querySelectorAll('.remove-assigned-cashier').forEach((btn) => {
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
                        message: 'Do you want to remove cashier to this branch?',
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
                    url: '/manager/cashier/assign/remove/' + cashierId,
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
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.manager_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Manager/manager_view_branch.blade.php ENDPATH**/ ?>
<?php $__env->startSection('page-title', 'City Burgers POS | Order Receipt'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid bg-white" style="overflow-x: hidden; " id="orderHistory">
        <div class="return p-3 text-dark">
            <a href="<?php echo e(route('manager-order-history.get', ['branch_id' => encrypt($branch->id)])); ?>" class="text-dark"><i
                    class="ph ph-arrow-left me-1"></i></a>
        </div>
        <div class="header-title">
            <div class="fs-6 fw-bold">Order Receipt</div>
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


        <div>
            <div class="fw-bold text-uppercase mt-4 mb-3">Order summary: <span
                    class="fw-normal text-capitalize ms-2"><?php echo e($formattedDate); ?></span></div>
            <div class="d-flex overflow-auto py-3 px-0" id="cardContainer">
                <?php $__currentLoopData = $uniqueProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card me-2">
                        <img src="<?php echo e(asset($food['image'])); ?>" class="card-img-top" alt="<?php echo e($food['item_name']); ?>">
                        <div class="card-body text-center">
                            <div class="food-name fw-bold"><?php echo e($food['item_name']); ?></div>
                        </div>
                        <div class="trending-popper fw-bold">
                            <span>Total Order:
                                <?php echo e($food['total_quantity']); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>


        <div>

            <div class="order-group">
                <div class="fw-bold text-uppercase mt-4 mb-3">Orders on: <span
                        class="fw-normal text-capitalize ms-2"><?php echo e($formattedDate); ?></span></div>
                <?php
                    $counter = 1; // Initialize a counter
                ?>

                <?php $__currentLoopData = $orderGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="accordion mb-2" id="accordion<?php echo e($group->id); ?>"
                        style="background-color: #ffffff; border: 1px solid #ccc;">
                        <div class="accordion-item" style="border: none;">
                            <h2 class="accordion-header" id="heading<?php echo e($group->id); ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo e($group->id); ?>" aria-expanded="true"
                                    aria-controls="collapse<?php echo e($group->id); ?>"
                                    style="color: #000; background-color: #ffffff; border: none;">
                                    Order #<?php echo e($group->customer_name); ?> - Payment Method: <?php echo e($group->payment_method); ?>

                                </button>
                            </h2>
                            <div id="collapse<?php echo e($group->id); ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading<?php echo e($group->id); ?>"
                                data-bs-parent="#accordion<?php echo e($group->id); ?>">
                                <div class="accordion-body" style="background-color: #ffffff; position: relative;">
                                    <?php if($group->payment_method === 'Gcash' || $group->payment_method === 'PayMaya'): ?>
                                        <a href="<?php echo e(route('download.receipt', $group->id)); ?>"
                                            style="position: absolute; top: 10px; right: 10px;" class="ms-2"
                                            title="Download e-receipt">
                                            <i class="fa-solid fa-file-arrow-down text-danger"
                                                style="font-size: 1.5rem;"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="ms-2"
                                            style="color: grey; cursor: not-allowed; position: absolute; top: 10px; right: 10px;">
                                            <i class="fa-solid fa-file-arrow-down text-danger"
                                                style="font-size: 1.5rem; opacity: 0.5;"></i>
                                        </span>
                                    <?php endif; ?>
                                    <div class="items-sold">
                                        <?php $__currentLoopData = $group->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div
                                                class="order-item d-flex align-items-center justify-content-between border-bottom py-2">
                                                <div class="order-details d-flex align-items-center">
                                                    <img src="<?php echo e($order->image); ?>" alt="<?php echo e($order->item); ?>"
                                                        class="img-fluid rounded" style="width: 50px; height: 50px;">
                                                    <div class="ms-3">
                                                        <div class="fw-bold"><?php echo e($order->item); ?> (<?php echo e($order->quantity); ?>x)
                                                        </div>
                                                        <?php
                                                            $drinks = [
                                                                'Mountain Dew',
                                                                'Royal',
                                                                'Coke',
                                                                'Water',
                                                                'Sprite',
                                                            ];
                                                        ?>
                                                        <?php if(!in_array($order->item, $drinks) && $order->sauce): ?>
                                                            <div class="text-muted">Sauce: <?php echo e($order->sauce); ?></div>
                                                        <?php endif; ?>
                                                        <div class="text-muted">Price:
                                                            &#8369;<?php echo e(number_format($order->price, 2)); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>



        </div>
    </div>

    <script>
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', () => {
                // Toggle the 'collapsed' class on the button
                button.classList.toggle('collapsed');
            });
        });
    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.manager_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Manager/manager_order_receipt.blade.php ENDPATH**/ ?>
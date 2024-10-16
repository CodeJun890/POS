<?php $__env->startSection('page-title', 'City Burgers POS | New Order'); ?>

<?php $__env->startSection('content'); ?>


    <div class="container-fluid p-0" id="orderContainer" style="overflow-x: hidden;">

        <div class="order-header p-3 w-100">
            <div class="bg-success w-100 rounded-2 cashier-branch">
                <div class="text-center text-light fw-bold py-2">Branch: <span class="fst-italic"><?php echo e($branch->name); ?></span>
                </div>
            </div>
            <?php echo $__env->make('Partial.cashier_navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div class="return mb-2 text-white">
                <a href="<?php echo e(route('cashier-dashboard.get')); ?>" class="text-white"><i
                        class="ph ph-arrow-circle-left me-1"></i></a> Back
                <div class="header-title" style="position: absolute; top:7px; left: 50%; transform: translateX(-50%);">
                    <div class="fs-6 fw-bold text-white text-center text-uppercase">Create Order</div>
                </div>
            </div>

            <div class="order-calculate bg-light p-3 rounded-4">
                <div class="d-flex justify-content-around">
                    <div class="text-center">
                        <div class="fs-6 fw-bold text-uppercase">Total</div>
                        <p class="m-0 total-sales">&#8369; <?php echo e(number_format($totalSalesToday, 2)); ?></p>
                    </div>

                    <div class="text-center">
                        <div class="fs-6 fw-bold text-uppercase">Profit</div>
                        <p class="m-0 total-profit">&#8369; <?php echo e(number_format($totalProfitToday, 2)); ?></p>
                    </div>

                </div>
            </div>
        </div>

        <div class="order-body rounded-5 bg-light">
            <div class="container">
                <div class="d-flex justify-content-center align-items-center w-100">
                    <div class="border border-2 bg-dark text-light border-dark p-1 px-2 rounded-4 mb-2 d-flex align-items-center"
                        style="position: relative;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrderStatus">
                        <i class="ph ph-check-square-offset me-1"></i>
                        Order Status
                        <div class="fw-bold text-light px-2 rounded-circle"
                            style="position: absolute; top:-10px; right: -8px; background: red;">
                            <span id="totalPendingOrders"> <?php echo e($todayOrdersCount); ?></span>
                        </div>
                    </div>

                </div>
                <div class="text-center mb-2">
                    <div class="date-today"><?php echo e(now()->format('l, F d, Y')); ?></div>
                </div>
                <div class="lead fw-bold">Select Order</div>
                <div class="order-counter w-100">
                    <div class="burger-counter" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop"
                        aria-controls="offcanvasTop">
                        <div class="fs-6 fw-semibold text-uppercase mb-4 fw-bold">Choose Burger</div>
                        <div class="burger-grid">
                            <div class="burger-box" data-burger="Buy 1 Take 1 Sliders" data-unique-id="burger-1">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/burger-1.jpg')); ?>" alt="Buy 1 Take 1 Sliders">
                                </div>
                                <div class="box-title">Buy 1 Take 1 Sliders</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 60</span>
                                </div>
                            </div>
                            <div class="burger-box" data-burger="Manila Burger" data-unique-id="burger-2">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/burger-2.jpg')); ?>" alt="Manila Burger">
                                </div>
                                <div class="box-title">Manila Burger</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 70</span>
                                </div>
                            </div>
                            <div class="burger-box" data-burger="Berlin Burger Steak" data-unique-id="burger-3">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/burger-3.jpg')); ?>" alt="Berlin Burger Steak">
                                </div>
                                <div class="box-title">Berlin Burger Steak</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 80</span>
                                </div>
                            </div>
                            <div class="burger-box" data-burger="New York Burger" data-unique-id="burger-4">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/burger-4.jpg')); ?>" alt="New York Burger">
                                </div>
                                <div class="box-title">New York Burger</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 110</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fries-counter">
                        <div class="fs-6 fw-semibold text-uppercase mb-4 fw-bold">Choose Fries</div>
                        <div class="fries-grid">
                            <div class="fries-box" data-fries="French Fries(Solo)" data-unique-id="fries-1">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/fries-1.jpg')); ?>" alt="French Fries">
                                </div>
                                <div class="box-title">French Fries (Solo)</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 70</span>
                                </div>
                            </div>
                            <div class="fries-box" data-fries="French Fries(Barkada)" data-unique-id="fries-2">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/fries-2.jpg')); ?>" alt="French Fries">
                                </div>
                                <div class="box-title">French Fries (Barkada)</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 100</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="drink-counter">
                        <div class="fs-6 fw-semibold text-uppercase mb-4 fw-bold">Choose Drinks</div>
                        <div class="drink-grid">
                            <div class="drink-box" data-drink="Mountain Dew" data-unique-id="drink-1">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/drink-1.jpg')); ?>" alt="Mountain Dew">
                                </div>
                                <div class="box-title">Mountain Dew</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 30</span>
                                </div>
                            </div>
                            <div class="drink-box" data-drink="Royal" data-unique-id="drink-2">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/drink-2.jpg')); ?>" alt="Royal">
                                </div>
                                <div class="box-title">Royal</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 30</span>
                                </div>
                            </div>
                            <div class="drink-box" data-drink="Sprite" data-unique-id="drink-3">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/drink-3.jpg')); ?>" alt="Sprite">
                                </div>
                                <div class="box-title">Sprite</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 30</span>
                                </div>
                            </div>
                            <div class="drink-box" data-drink="Coke" data-unique-id="drink-4">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/drink-4.jpg')); ?>" alt="Coke">
                                </div>
                                <div class="box-title">Coke</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 30</span>
                                </div>
                            </div>
                            <div class="drink-box" data-drink="Coke" data-unique-id="drink-5">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/drink-5.jpg')); ?>" alt="Water">
                                </div>
                                <div class="box-title">
                                    <?php echo e($branch->name === 'CvSU Main Indang' ? 'Water (CvSU)' : 'Water'); ?></div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; <?php echo e($branch->name === 'CvSU Main Indang' ? '15' : '30'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="addons-counter">
                        <div class="fs-6 fw-semibold text-uppercase mb-4 fw-bold">Choose ADD-ONS</div>
                        <div class="addons-grid">
                            <div class="addons-box" data-drink="Rice" data-unique-id="addons-1">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/addons-rice.jpg')); ?>" alt="Egg">
                                </div>
                                <div class="box-title">Rice</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 15</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Egg" data-unique-id="addons-2">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/addons-1.jpg')); ?>" alt="Egg">
                                </div>
                                <div class="box-title">Egg</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 15</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Lettuce" data-unique-id="addons-3">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/addons-2.jpg')); ?>" alt="Lettuce">
                                </div>
                                <div class="box-title">Lettuce</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Tomato" data-unique-id="addons-4">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/addons-3.jpg')); ?>" alt="Tomato">
                                </div>
                                <div class="box-title">Tomato</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Garlic Mayo" data-unique-id="addons-5">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-1.png')); ?>" alt="Garlic Mayo">
                                </div>
                                <div class="box-title">Garlic Mayo</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Garlic BBQ" data-unique-id="addons-6">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-3.jpg')); ?>" alt="Garlic BBQ">
                                </div>
                                <div class="box-title">Garlic BBQ</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Kebab" data-unique-id="addons-7">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-2.jpg')); ?>" alt="Kebab">
                                </div>
                                <div class="box-title">Kebab</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Yangnyeom" data-unique-id="addons-8">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-4.jpg')); ?>" alt="Yangnyeom">
                                </div>
                                <div class="box-title">Yangnyeom</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Cheese Sauce" data-unique-id="addons-9">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-5.jpg')); ?>" alt="Cheese Sauce">
                                </div>
                                <div class="box-title">Cheese Sauce</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                            <div class="addons-box" data-drink="Hot Sauce" data-unique-id="addons-10">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-6.png')); ?>" alt="Ketchup & Mayo">
                                </div>
                                <div class="box-title">Hot Sauce</div>
                                <div class="box-price price fw-bold">
                                    <span>&#8369; 10</span>
                                </div>
                            </div>
                        </div>
                        <div id="order-buttons">
                            <div class="order-list">
                                <i class="ph ph-list-checks me-1 text-white"></i>
                                <a href="" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom">Check
                                    Orders</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-------- COUNT ORDER & ASSIGN SAUCES ------->
            <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" data-bs-backdrop="static"
                aria-labelledby="offcanvasTopLabel">
                <div class="offcanvas-header">
                    <div class="fs-6 fw-semibold text-uppercase mb-2">Specify Order</div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body offcanvas-body-top-overflow" style="overflow-y: hidden;">
                    <div class="selected-item-quantity">
                        <div class="text-capitalize fw-normal text-center mb-2">Quantity</div>
                        <div class="quantity">
                            <a href="#" class="quantity__minus"><span>-</span></a>
                            <input name="quantity" type="text" class="quantity__input" value="1">
                            <a href="#" class="quantity__plus"><span>+</span></a>
                        </div>
                    </div>
                    <div id="selected-item"
                        class="text-center d-flex flex-column justify-content-center align-items-center mb-3 border border-1 rounded-3 py-2">
                        <img id="selected-item-img" src="" alt="Selected Item" class="img-fluid w-50">
                        <div id="selected-item-title" class="fw-bold"></div>
                    </div>
                    <div class="sauce-counter">
                        <div class="sauce-grid">
                            <div class="sauce-box" data-burger="Garlic Mayo">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-1.png')); ?>" alt="Garlic Mayo">
                                </div>
                                <div class="box-title">Garlic Mayo</div>
                            </div>

                            <div class="sauce-box" data-burger="Garlic BBQ">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-3.jpg')); ?>" alt="Garlic BBQ">
                                </div>
                                <div class="box-title">Garlic BBQ</div>
                            </div>
                            <div class="sauce-box" data-burger="Kebab">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-2.jpg')); ?>" alt="Kebab">
                                </div>
                                <div class="box-title">Kebab</div>
                            </div>
                            <div class="sauce-box" data-burger="Yangnyeom">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-4.jpg')); ?>" alt="Yangnyeom">
                                </div>
                                <div class="box-title">Yangnyeom</div>
                            </div>
                            <div class="sauce-box" data-burger="Cheese Sauce">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-5.jpg')); ?>" alt="Cheese Sauce">
                                </div>
                                <div class="box-title">Cheese Sauce</div>
                            </div>
                            <div class="sauce-box" data-burger="Hot Sauce">
                                <div class="box-content">
                                    <img src="<?php echo e(asset('assets/images/sauce-6.png')); ?>" alt="Ketchup & Mayo">
                                </div>
                                <div class="box-title">Hot Sauce</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-evenly mt-3 gap-2" id="burgerSelectSauceOffcanvas">
                        <button id="cancelButton" class="bg-secondary border-0 px-5 py-2 text-white rounded-5"
                            data-bs-dismiss="offcanvas">Cancel</button>
                        <button class="bg-success border-0 px-5 py-2 text-white rounded-5">Save</button>
                    </div>
                </div>
            </div>

            <!-------- CHECK ORDER ------->
            <div class="offcanvas offcanvas-bottom h-75" tabindex="-1" id="offcanvasBottom"
                aria-labelledby="offcanvasBottomLabel">
                <div class="offcanvas-header">
                    <div class="d-flex align-items-center">
                        <i class="ph ph-shopping-cart me-1"></i>
                        <h5 class="offcanvas-title fs-6 text-uppercase fw-semibold" id="offcanvasBottomLabel">Checkout
                            Order
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body small" id="checkout-offcanvas-body">

                    <div class="mt-4 d-flex align-items-center justify-content-center" id="noOrderMessage"
                        style="font-size: 0.8rem; z-index: 9999;">
                        <i class="ph ph-x-circle me-1" style="font-size: 1.25rem;"></i> No orders added.
                    </div>

                </div>

                <div class="offcanvas-footer d-flex justify-content-center py-3">
                    <button id="submitOrdersButton" class="btn btn-success border-0 rounded-1 d-none">Submit
                        Order</button>
                </div>
            </div>
            <!-- PAYMENT METHOD -->
            <div class="offcanvas offcanvas-bottom h-50" id="offcanvasPayment" tabindex="-1"
                aria-labelledby="offcanvasPaymentLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasPaymentLabel">Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Choose Payment Method:</label>
                        <div>
                            <input type="radio" name="paymentMethod" value="Cash" checked
                                onclick="togglePhotoCapture(false)"> Cash<br>
                            <input type="radio" name="paymentMethod" value="PayMaya"
                                onclick="togglePhotoCapture(true)">
                            PayMaya<br>
                            <input type="radio" name="paymentMethod" value="Gcash"
                                onclick="togglePhotoCapture(true)">
                            Gcash<br>
                        </div>
                    </div>
                    <div class="mb-3 d-flex flex-column" id="photoCaptureSection">
                        <label class="form-label">Capture Payment Proof: <span class="fileName"></span></label>
                        <div class="d-flex justify-content-around">
                            <button id="capturePhotoButton" type="button" class="btn btn-secondary" disabled>
                                <i class="fas fa-camera"></i> Take Photo
                            </button>
                            <button id="paymentMethodSubmitButton" class="btn btn-primary" disabled>Submit
                                Payment</button>
                        </div>
                        <input type="file" class="form-control" id="eReceiptInput" accept="image/*" capture="camera"
                            style="display: none;">
                    </div>


                </div>
            </div>
            <!-- ORDER STATUS -->
            <div class="offcanvas offcanvas-bottom h-75" id="offcanvasOrderStatus" tabindex="-1"
                aria-labelledby="offcanvasOrderStatusLabel">
                <div class="offcanvas-header">
                    <div class="d-flex align-items-center">
                        <i class="ph-package me-1" style="font-size: 1.4rem;"></i>
                        <h5 class="offcanvas-title fs-6 text-uppercase fw-semibold" id="offcanvasOrderStatusLabel">Order
                            Status</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <!-- Pending Orders Container -->
                    <div class="pending-orders-list"></div>

                    <!-- No Pending Orders Message -->
                    <div class="mt-4 d-flex align-items-center justify-content-center" id="noPendingOrderMessage"
                        style="font-size: 0.8rem;">
                        <i class="ph ph-x-circle me-1" style="font-size: 1.25rem;"></i> No pending orders.
                    </div>
                </div>
            </div>


        <?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.cashier_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Cashier/cashier_order.blade.php ENDPATH**/ ?>
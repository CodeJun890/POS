<?php $__env->startSection('page-title', 'City Burgers POS | Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-0" id="dashboardContainer">
        <div class="dashboard-header p-3">
            <div class="bg-success w-100 rounded-2">
                <div class="cashier-branch text-center text-light fw-bold py-2">Branch: <span
                        class="fst-italic"><?php echo e($branch->name); ?></span></div>
            </div>
            <?php echo $__env->make('Partial.cashier_navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="lead fw-bold text-white">Dashboard</div>
            <div class="d-flex overflow-auto py-3 px-0" id="cardContainer">
                <?php if($trendingFood->isNotEmpty() || $trendingDrink): ?>
                    <?php if($trendingFood->isNotEmpty()): ?>
                        <?php $__currentLoopData = $trendingFood; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card me-2">
                                <img src="<?php echo e(asset($food->image)); ?>" class="card-img-top" alt="<?php echo e($food->item); ?>">
                                <div class="card-body text-center">
                                    <div class="food-name fw-bold"><?php echo e($food->item); ?></div>
                                    <p class="m-0 text-secondary" style="font-size: 0.6rem">Sauce: <?php echo e($food->sauce); ?></p>
                                </div>
                                <div class="trending-popper <?php echo e($index === 0 ? 'popular' : 'buzzing'); ?> fw-bold">
                                    <span>
                                        <i class="fa-solid fa-<?php echo e($index === 0 ? 'trophy' : 'fire'); ?>"></i>
                                        <?php echo e($index === 0 ? 'Top Choice' : '2nd Choice'); ?>

                                    </span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if($trendingDrink): ?>
                        <div class="card me-2">
                            <img src="<?php echo e(asset($trendingDrink->image)); ?>" class="card-img-top"
                                alt="<?php echo e($trendingDrink->item); ?>">
                            <div class="card-body text-center">
                                <div class="food-name fw-bold"><?php echo e($trendingDrink->item); ?></div>
                            </div>
                            <div class="trending-popper best-selling fw-bold">
                                <span><i class="fa-solid fa-thumbs-up"></i> Favorite Drink</span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>



        <div class="dashboard-body bg-light" style="overflow-x: hidden;">
            <div class="container">
                <div class="tabs">
                    <input type="radio" id="radio-1" class="menu-tabs" name="tabs" checked />
                    <label class="tab" for="radio-1">Food Menu</label>

                    <input type="radio" id="radio-2" class="menu-tabs" name="tabs" />
                    <label class="tab" for="radio-2">Inventory</label>

                    <span class="glider"></span>
                </div>

                <div class="food-menu mt-4 w-100 px-3">
                    <div class="fs-6 fw-bold mb-1">Food Menu</div>

                    <div class="grid-container">
                        <div class="card">
                            <div class="image-card-container rounded-4 shadow-md">
                                <img src="<?php echo e(asset('assets/images/burger-1.jpg')); ?>" class="card-img-top img-square"
                                    alt="...">
                            </div>
                            <div class="card-body p-0 pt-1">
                                <div class="food-name fw-bold">Buy 1 Take 1 Sliders</div>
                                <p class="m-0 text-secondary" style="font-size: 0.6rem">Indulge in 80 grams of Juicy Beef
                                    Patty and Fresh Veggies - A Delicious Duo</p>
                                <div class="price fw-bold">
                                    <span>&#8369; 60</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="image-card-container rounded-4 shadow-md">
                                <img src="<?php echo e(asset('assets/images/burger-2.jpg')); ?>" class="card-img-top img-square"
                                    alt="...">
                            </div>
                            <div class="card-body p-0 pt-1">
                                <div class="food-name fw-bold">Manila Burger</div>
                                <p class="m-0 text-secondary" style="font-size: 0.6rem">130 grams of succulent beef, gooey
                                    cheese and crisp vegetables - Get ready for a burst of flavors!</p>
                                <div class="price fw-bold">
                                    <span>&#8369; 70</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="image-card-container rounded-4 shadow-md">
                                <img src="<?php echo e(asset('assets/images/burger-3.jpg')); ?>" class="card-img-top img-square"
                                    alt="...">
                            </div>
                            <div class="card-body p-0 pt-1">
                                <div class="food-name fw-bold">Berlin Burger Steak</div>
                                <p class="m-0 text-secondary" style="font-size: 0.6rem">130 grams Beef Patty Delight with a
                                    perfect blend of juicy beef, fluffy rice, egg, rich gravy, and crispy fries!</p>
                                <div class="price fw-bold">
                                    <span>&#8369; 80</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="image-card-container rounded-4 shadow-md">
                                <img src="<?php echo e(asset('assets/images/burger-4.jpg')); ?>" class="card-img-top img-square"
                                    alt="...">
                            </div>
                            <div class="card-body p-0 pt-1">
                                <div class="food-name fw-bold">New York Burger</div>
                                <p class="m-0 text-secondary" style="font-size: 0.6rem">Indulge in our Ultimate Burger:
                                    Double Juicy Pattie, Melted Cheese, Crispy Bacon and Veggies</p>
                                <div class="price fw-bold">
                                    <span>&#8369; 110</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="inventory mt-4 w-100 px-3">
                    <div class="fs-6 fw-bold mb-1">Inventory</div>

                    
                </div>

                <div class="make-order">
                    <i class="ph ph-plus me-1 text-white"></i>
                    <a href="<?php echo e(route('cashier-order.get')); ?>">Create Order</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const foodMenu = document.querySelector('.food-menu');
            const inventory = document.querySelector('.inventory');
            const tabs = document.querySelectorAll('.menu-tabs');
            const labels = document.querySelectorAll('.tab');

            tabs.forEach((radio, index) => {
                radio.addEventListener('input', () => {
                    labels.forEach(label => label.classList.remove('active'));
                    labels[index].classList.add('active');

                    if (radio.id === 'radio-2') {
                        foodMenu.style.transition = 'transform 0.5s ease';
                        foodMenu.style.transform = 'translateX(100%)'; // Slide out
                        inventory.style.transition = 'transform 0.5s ease';
                        inventory.style.transform = 'translateX(0%)'; // Slide in
                    } else {
                        inventory.style.transition = 'transform 0.5s ease';
                        inventory.style.transform = 'translateX(-100%)'; // Slide out
                        foodMenu.style.transition = 'transform 0.5s ease';
                        foodMenu.style.transform = 'translateX(0%)'; // Slide in
                    }
                });
            });
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.cashier_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Cashier/cashier_dashboard.blade.php ENDPATH**/ ?>
<nav id="cashierNav" class="d-flex justify-content-between align-items-center py-3 px-1 text-white">
    <div class="name d-flex align-items-center">
        <i class="ph ph-user-circle"></i>
        <span class="text-uppercase ms-2"><?php echo e($user->role); ?></span>
    </div>


    <div class="d-flex align-items-center gap-2">
        <div>
            Hi, <span class="fw-bold"><?php echo e(explode(' ', $user->name)[0]); ?></span>
        </div>
        <i class="ph ph-list menu-header-dashboard" data-bs-toggle="offcanvas" data-bs-target="#sidebarCashier"></i>
    </div>
</nav>

<div class="offcanvas offcanvas-end text-bg-dark w-50" tabindex="-1" id="sidebarCashier"
    aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title fw-bold" id="offcanvasRightLabel">Cashier Menu</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <i class="ph-house"></i>
                <a class="nav-link text-white" href="<?php echo e(route('cashier-dashboard.get')); ?>">
                    Dashboard
                </a>
            </li>
            <li class="nav-item mb-3">
                <i class="ph-user-circle"></i>
                <a class="nav-link text-white" href="<?php echo e(route('cashier-profile.get')); ?>">
                    My Profile
                </a>
            </li>
            <li class="nav-item mb-3">
                <i class="ph-shopping-cart"></i>
                <a class="nav-link text-white" href="<?php echo e(route('cashier-order.get')); ?>">
                    Create Order
                </a>
            </li>
            <li class="nav-item mb-3">
                <i class="ph-clock-counter-clockwise"></i>
                <a class="nav-link text-white" href="<?php echo e(route('cashier-order-history.get')); ?>">
                    Order History
                </a>
            </li>

            <li class="nav-item">
                <i class="ph-sign-out"></i>
                <a id="logout-btn" class="nav-link text-danger">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Partial/cashier_navbar.blade.php ENDPATH**/ ?>
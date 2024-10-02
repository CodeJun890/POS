<?php $__env->startSection('page-title', 'City Burgers POS | Login'); ?>

<?php $__env->startSection('content'); ?>

    <div class="login-header d-flex flex-column justify-content-center py-5 ps-3">
        <div class="fw-bold text-white text-center text-uppercase fst-italic" style="font-size: 1.8rem">City Burgers</div>
        <div class="fw-bold text-white text-center fst-italic fst-underline" style="font-size: 1.8rem">Mobile <span
                style="color: #dc3545; font-size: 2rem;">POS</span> System</div>
        <p class="text-light text-center">Sign in to your Account</p>
    </div>
    <div class="login-input">
        <form action="<?php echo e(route('login.post')); ?>" method="POST" class="form-md px-1">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <input id="email" name="email" class="form-control" type="email" required>
                <label for="email">Email</label>
            </div>

            <div class="form-group ">
                <input id="password" name="password" class="form-control" type="password" autocomplete="off" required>
                <label for="password">Password</label>
                <i class="ph ph-eye-slash" id="togglePassword" style="cursor: pointer;"></i>
            </div>
            <div class="my-2 d-flex justify-content-between align-items-center mx-auto" style="max-width: 320px">
                <div class="d-flex align-items-center">
                    <input type="checkbox" name="remember" id="rememberMe" style="cursor: pointer;">
                    <label for="rememberMe" style="cursor: pointer;" class="fw-normal ms-1">Remember Me</label>
                </div>
                <a href="#" style="color: #00832c; text-decoration:none;">Forgot Password?</a>
            </div>
            <div class="form-group">
                <button id="loginBtn" class="btn btn-primary w-100">Login</button>
            </div>

        </form>

    </div>
    <?php echo $__env->make('Partial.home_footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.login_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Auth/login.blade.php ENDPATH**/ ?>
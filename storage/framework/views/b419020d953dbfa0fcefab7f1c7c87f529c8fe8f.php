<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!--- LOCAL CSS ---->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <!--- FONTAWESOME ICON CDN ---->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <!--- PHOSPHOR ICON CDN ---->
    <link href="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/css/icons.min.css" rel="stylesheet">
    <!--- OPEN SANS CDN (GOOGLE FONTS) ---->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <!--- CITY BURGERS LOGO ---->
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/logo.jpg')); ?>">
    <!--- BOOTSTRAP CSS CDN ---->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--- IZITOAST CSS ---->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <!--- SELECT2 CSS ---->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <title><?php echo $__env->yieldContent('page-title', 'City Burgers POS'); ?></title>
    <style>
        .sauce-graph,
        .weekly-sales-graph {
            background-color: #343a40;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .text-light {
            color: #ffffff;
        }

        .select2-container {
            width: 100% !important;
            flex: 1 1 auto;
        }

        .select2-container .select2-selection--single {
            height: 100%;
            line-height: inherit;
            padding: 0.3rem 1rem;
        }

        .select2-selection__arrow b {
            margin-top: 0.2rem !important;
        }
    </style>
</head>

<body>

    <div class="manager-container">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <div class="announcement-container d-none">
        <iframe src="https://giphy.com/embed/Z5xk7fGO5FjjTElnpT" width="180" height="180" style=""
            frameBorder="0" class="giphy-embed"></iframe>
        <div class="lead mt-3 fw-bold fst-italic text-uppercase">
            Use smaller screen size phone <i class="fa-solid fa-mobile-screen ms-2"
                style="font-size: 2rem; color:rgb(92, 92, 92);"></i>
        </div>
        <?php if(auth()->guard()->check()): ?>
            <div class="d-flex justify-content-center align-items-center">
                <div class="bg-danger rounded-2 fst-italic mt-2 p-2 px-3">
                    <a href="<?php echo e(route('logout.get')); ?>" class="text-light text-decoration-none fw-bold ">Logout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!--- JQUERY CDN ---->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!--- PHOSPHOR ICON JS CDN ---->
    <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>
    <!--- BOOTSTRAP JS CDN ---->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--- IZITOAST JS ---->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <!--- CHART JS ---->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!--- SELECT2 JS ---->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <?php if(session('success')): ?>
        <script>
            iziToast.success({
                title: 'Success',
                message: "<?php echo e(session('success')); ?>",
                position: 'topCenter'
            });
        </script>
    <?php endif; ?>

    <script>
        function checkScreenWidth() {
            if (window.innerWidth > 500) {
                document.querySelector('.announcement-container').classList.remove('d-none');
            } else {
                document.querySelector('.announcement-container').classList.add('d-none');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logout-btn');

            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default action
                    iziToast.question({
                        timeout: false,
                        close: false,
                        overlay: true,
                        displayMode: 'once',
                        id: 'question',
                        zindex: 3000,
                        title: 'Are you sure?',
                        message: 'Do you really want to log out?',
                        position: 'center',
                        buttons: [
                            ['<button><b>Yes</b></button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');
                                // Redirect to logout route
                                window.location.href = "<?php echo e(route('logout.get')); ?>";
                            }, true],
                            ['<button>No</button>', function(instance, toast) {
                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');
                            }]
                        ]
                    });
                });
            }
        });

        $('#cashier').select2({
            dropdownParent: $('#assignCashier')
        });
    </script>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Layout/manager_layout.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--- LOCAL CSS ---->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
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
    <link rel="shortcut icon" href="{{ asset('assets/images/logo.jpg') }}">
    <!--- BOOTSTRAP CSS CDN ---->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--- IZITOAST CSS ---->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <title>@yield('page-title', 'City Burgers POS')</title>
</head>

<body>
    <div class="login-container">
        @yield('content')
    </div>
    <div class="announcement-container d-none">
        <iframe src="https://giphy.com/embed/Z5xk7fGO5FjjTElnpT" width="180" height="180" style=""
            frameBorder="0" class="giphy-embed"></iframe>
        <div class="lead mt-3 fw-bold fst-italic text-uppercase">
            Use smaller screen size phone <i class="fa-solid fa-mobile-screen ms-2"
                style="font-size: 2rem; color:rgb(92, 92, 92);"></i>
        </div>
        @auth
            <div class="d-flex justify-content-center align-items-center">
                <div class="bg-danger rounded-2 fst-italic mt-2 p-2 px-3">
                    <a href="{{ route('logout.get') }}" class="text-light text-decoration-none fw-bold ">Logout</a>
                </div>
            </div>
        @endauth
    </div>
    <!--- PHOSPHOR ICON JS CDN ---->
    <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>
    <!--- BOOTSTRAP JS CDN ---->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--- IZITOAST JS ---->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    @if (session('error'))
        {
        <script>
            iziToast.error({
                title: 'Error',
                message: "{{ session('error') }}",
                position: 'topCenter'
            });
        </script>

        }
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("password");

            togglePassword.addEventListener("click", function() {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                this.classList.toggle("ph-eye");
                this.classList.toggle("ph-eye-slash");
            });
        });

        function checkScreenWidth() {
            if (window.innerWidth > 500) {
                document.querySelector('.announcement-container').classList.remove('d-none');
            } else {
                document.querySelector('.announcement-container').classList.add('d-none');
            }
        }

        checkScreenWidth();
    </script>
</body>

</html>

@extends('Layout.cashier_layout')

@section('page-title', 'City Burgers POS | My Profile')

@section('content')
    <div class="container-fluid p-0" id="profileContainer">
        <div class="profile-header p-3">
            @include('Partial.cashier_navbar')
            <div class="profile-picture d-flex align-items-center flex-column justify-content-center ">
                <canvas id="profile-pic" class="rounded-circle" width="100" height="100"></canvas>
                <p class="mt-2 mb-0 text-muted fs-6">&commat;{{ explode(' ', $user->name)[0] }}</p>

                <div class="fs-3 fw-bold">{{ $user->name }}</div>
                <div class="mb-2">
                    {{ $user->contact_number }} | Joined {{ $user->created_at->format('F Y') }}
                </div>
            </div>
            <div class="information-profile p-3 rounded-3 mt-3" style="background: #212225;">
                <div class="fs-6 fw-bold text-light">Information</div>
                <div>
                    <div class="d-flex align-items-center justify-content-between text-light mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-envelope"></i>
                            <span>Email</span>
                        </div>
                        <div>
                            {{ $user->email }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-light mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-phone"></i>
                            <span>Phone</span>
                        </div>
                        <div>
                            {{ $user->contact_number }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-light mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>Joined</span>
                        </div>
                        <div>
                            {{ $user->created_at->format('F Y') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="information-profile p-3 rounded-3 mt-3" style="background: #212225;">
                <div class="fs-6 fw-bold text-light">Works At</div>
                <div>
                    <div class="d-flex align-items-center justify-content-between text-light mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Cashier</span>
                        </div>
                        <div>
                            City Burger's
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="text-center footer-profile">
            <span>
                &copy; 2024 City Burgers POS System. All Rights Reserved.
            </span>
        </footer>

    </div>
    <script>
        function generateProfilePic(fullName) {
            const canvas = document.getElementById('profile-pic');
            const ctx = canvas.getContext('2d');

            // Set the background color
            ctx.fillStyle = 'graphite'; // Light gray background
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Set the text properties
            ctx.fillStyle = '#ffffff'; // White text
            ctx.font = 'bold 40px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Split the full name into parts
            const nameParts = fullName.trim().split(' ');

            // Check for "Jr", "jr", "JR" at the end
            if (nameParts.length > 1 &&
                (nameParts[nameParts.length - 1].toLowerCase() === 'jr' ||
                    nameParts[nameParts.length - 1].toLowerCase() === 'sr')) {
                // Remove the last part (Jr. or Sr.) and use the second last part as last name
                const firstName = nameParts[0];
                const lastName = nameParts[nameParts.length - 2]; // Use the word before Jr. or Sr.

                // Get initials
                const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();

                // Draw the text on the canvas
                ctx.fillText(initials, canvas.width / 2, canvas.height / 2);
            } else {
                // Normal case: just take the first and last name
                const firstName = nameParts[0];
                const lastName = nameParts[nameParts.length - 1];

                // Get initials
                const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();

                // Draw the text on the canvas
                ctx.fillText(initials, canvas.width / 2, canvas.height / 2);
            }
        }

        // Safely pass the user's name to the JavaScript function
        const userName = @json($user->name);
        if (userName) {
            generateProfilePic(userName);
        }
    </script>
@endsection

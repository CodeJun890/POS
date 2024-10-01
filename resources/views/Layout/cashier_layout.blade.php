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

    <div class="cashier-container">
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
    <!--- JQUERY CDN ---->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!--- PHOSPHOR ICON JS CDN ---->
    <script src="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/index.min.js"></script>
    <!--- BOOTSTRAP JS CDN ---->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--- IZITOAST JS ---->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

    <script>
        function checkScreenWidth() {
            if (window.innerWidth > 500) {
                document.querySelector('.announcement-container').classList.remove('d-none');
            } else {
                document.querySelector('.announcement-container').classList.add('d-none');
            }
        }
        checkScreenWidth();
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
                                window.location.href = "{{ route('logout.get') }}";
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

        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // Get the corresponding radio ID
                const radioId = this.parentElement.getAttribute('for');
                document.getElementById(radioId).checked = true;

                // Move the glider based on the checked radio button
                const glider = document.querySelector('.glider');
                if (radioId === 'radio-1') {
                    glider.style.transform = 'translateX(0)';
                } else if (radioId === 'radio-2') {
                    glider.style.transform = 'translateX(100%)';
                } else if (radioId === 'radio-3') {
                    glider.style.transform = 'translateX(200%)';
                }

                // Allow the sliding animation to complete before navigating
                setTimeout(() => {
                    window.location.href = this.href; // Navigate to the link
                }, 300); // Adjust this time based on your CSS transition duration
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.getElementById("toggle");
            const toggleMini = document.getElementById("toggle-mini");
            const navbarCollapse = document.getElementById("navbarCollapse");

            if (toggle && toggleMini && navbarCollapse) {
                toggle.addEventListener("click", function() {
                    this.classList.toggle("is-active");
                    navbarCollapse.classList.toggle("is-active");
                });

                toggleMini.addEventListener("click", function() {
                    toggle.classList.toggle("is-active");
                    navbarCollapse.classList.toggle("is-active");
                });
            }
        });

        $(document).ready(function() {
            // Check if the necessary elements exist
            if ($('.quantity__input').length && $('#offcanvasTop').length && $('.burger-box').length) {
                // Handle quantity changes
                const quantityInput = $('.quantity__input');

                // Generic function for handling item selection (Burger or Fries)
                function setupItemSelection(itemBoxes, selectedTitleId, selectedImageId) {
                    itemBoxes.each(function() {
                        $(this).on('click', function() {
                            // Remove green border from all boxes
                            itemBoxes.removeClass('border border-success');

                            // Add green border to the clicked box
                            $(this).addClass('border border-success');

                            // Get item details
                            const itemTitle = $(this).find('.box-title').text();
                            const itemImage = $(this).find('img').attr('src');

                            // Update offcanvas with selected item details
                            $('#selected-item-title').text(itemTitle);
                            $('#selected-item-img').attr('src', itemImage);
                            quantityInput.val(1); // Reset quantity to 1
                            $('#selected-item-quantity-display').text(1); // Start with 1 item

                            // Open the offcanvas
                            const offcanvas = new bootstrap.Offcanvas(document.getElementById(
                                'offcanvasTop'));
                            offcanvas.show();
                        });
                    });
                }

                // Burger selection handling
                const burgerBoxes = $('.burger-box');
                setupItemSelection(burgerBoxes, 'selected-burger-title', 'selected-burger-img');

                // Fries selection handling
                const friesBoxes = $('.fries-box');
                setupItemSelection(friesBoxes, 'selected-fries-title', 'selected-fries-img');

                // Drink selection handling
                const drinkBoxes = $('.drink-box');
                setupItemSelection(drinkBoxes, 'selected-drink-title', 'selected-drink-img');

                // Close offcanvas event
                const offcanvasElement = document.getElementById('offcanvasTop');
                offcanvasElement.addEventListener('hidden.bs.offcanvas', function() {
                    // Remove any backdrop styles
                    document.body.classList.remove('offcanvas-open');
                    const backdrop = document.querySelector('.offcanvas-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                });

                // Sauce selection handling
                $('.sauce-box').on('click', function() {
                    // Remove green border from all sauce boxes
                    $('.sauce-box').removeClass('border border-success');

                    // Add green border to the clicked sauce box
                    $(this).addClass('border border-success');
                });
            }
        });

        function togglePhotoCapture(show) {
            const photoCaptureSection = document.getElementById('photoCaptureSection');
            const capturePhotoButton = document.getElementById('capturePhotoButton');
            const fileNameDisplay = document.querySelector('.fileName');

            if (show) {
                capturePhotoButton.disabled = false; // Enable the Take Photo button
            } else {
                capturePhotoButton.disabled = true; // Disable the Take Photo button
                document.getElementById('eReceiptInput').value = ''; // Reset the file input when switching back to Cash
                fileNameDisplay.textContent = '';
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Check if the button and file input elements exist
            const capturePhotoButton = document.getElementById('capturePhotoButton');
            const fileInput = document.getElementById('eReceiptInput');

            if (capturePhotoButton && fileInput) {
                capturePhotoButton.addEventListener('click', function() {
                    // Trigger the file input click to open the camera
                    fileInput.click();

                    // Handle file selection
                    fileInput.onchange = function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            // Optionally display the captured photo preview
                            const reader = new FileReader();
                            reader.readAsDataURL(file);
                        }
                    };
                });
            } else {
                console.warn('Capture photo button or file input does not exist.');
            }

        })
        document.addEventListener('DOMContentLoaded', function() {
            // Check if essential elements exist before executing the code
            if (
                document.querySelector('#burgerSelectSauceOffcanvas .bg-success') &&
                document.querySelector('#offcanvasBottom .offcanvas-body') &&
                document.querySelector('#submitOrdersButton') &&
                document.querySelector('.quantity__input')
            ) {
                let orders = [];
                const saveButton = document.querySelector('#burgerSelectSauceOffcanvas .bg-success');
                const checkoutOffcanvasBody = document.querySelector('#offcanvasBottom .offcanvas-body');
                const submitOrdersButton = document.querySelector('#submitOrdersButton');
                const paymentSubmitButton = document.querySelector('#paymentMethodSubmitButton');

                // Add event listener to the "Save" button in the sauce offcanvas
                saveButton.addEventListener('click', function() {
                    const selectedItemTitle = document.getElementById('selected-item-title').textContent
                        .trim();
                    const selectedItemImage = document.getElementById('selected-item-img').src;
                    const selectedQuantity = document.querySelector('.quantity__input').value;
                    const selectedPrice = document.querySelector('.box-price span').textContent.trim();
                    const submitOrdersButton = document.querySelector('#submitOrdersButton');


                    if (orders.length === 0) {
                        if (submitOrdersButton) { // Ensure the element exists before trying to access classList
                            submitOrdersButton.classList.remove('d-none'); // Show the message
                        }

                    }
                    // Determine if the selected item is a drink
                    const isDrink = document.querySelector(
                        '.burger-box.active, .fries-box.active, .drink-box.active')?.classList.contains(
                        'drink-box');

                    // Set selectedSauce based on whether the item is a drink
                    const selectedSauce = isDrink ? null : (document.querySelector(
                        '.sauce-box.selected .box-title')?.textContent.trim() || 'No Sauce');

                    if (!selectedItemTitle) {
                        iziToast.warning({
                            title: 'Warning',
                            message: 'Please select an item first!',
                            position: 'topRight',
                            timeout: 3000,
                        });
                        return;
                    }

                    const quantity = parseInt(selectedQuantity);
                    if (isNaN(quantity) || quantity < 1) {
                        iziToast.error({
                            title: 'Error',
                            message: 'Please enter a valid quantity!',
                            position: 'topRight',
                            timeout: 3000,
                        });
                        return;
                    }

                    // Push the order to the state
                    orders.push({
                        item: selectedItemTitle,
                        quantity: quantity,
                        sauce: selectedSauce, // This will be null if it's a drink
                        image: selectedItemImage,
                        price: selectedPrice
                    });

                    // Clear and update the Checkout Order offcanvas with current orders
                    renderOrders();

                    // Clear previous sauce selection
                    document.querySelectorAll('.sauce-box').forEach((sauce) => {
                        sauce.classList.remove('selected');
                        sauce.classList.remove('border'); // Reset active class on all sauce options
                        sauce.classList.remove(
                            'border-success'); // Reset active class on all sauce options
                    });

                    // Clear the sauce display in the offcanvas if a drink is selected
                    if (isDrink) {
                        document.querySelector('.sauce-grid').style.display = 'none'; // Hide sauce options
                        document.querySelector('.sauce-box.selected .box-title').textContent =
                            ''; // Clear the displayed sauce title if needed
                    }

                    // Show iziToast notification
                    iziToast.success({
                        title: 'Order Added!',
                        message: `${selectedItemTitle} has been added to your cart.`,
                        position: 'topRight',
                        timeout: 2000,
                        backgroundColor: '#5cb85c',
                        icon: 'fas fa-check-circle',
                        titleSize: '14px',
                        messageSize: '12px',
                        titleLineHeight: '20px',
                        messageLineHeight: '18px',
                        layout: 2,
                        balloon: false,
                        theme: 'dark',
                        iconColor: '#fff',
                        progressBar: false,
                        borderRadius: '6px',
                        padding: '10px 20px',
                    });

                    // Close the sauce selection offcanvas
                    const sauceOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(
                        'offcanvasTop'));
                    sauceOffcanvas.hide();
                });



                // Function to render orders in the checkout offcanvas
                function renderOrders() {
                    checkoutOffcanvasBody.innerHTML = '';
                    let totalPrice = 0; // Initialize total price

                    // Create and insert the customer name input HTML
                    const customerNameContainer = `
                        <div class="mb-3 d-none" id="customerNameContainer">
                            <label for="customerName" class="form-label">Customer Name (Optional)</label>
                            <input type="text" id="customerName" class="form-control" placeholder="Enter customer name" />
                        </div>
                    `;
                    checkoutOffcanvasBody.insertAdjacentHTML('beforeend', customerNameContainer);
                    // Get the noOrderMessage element
                    const customerName = document.getElementById('customerNameContainer');

                    if (orders.length === 0) {
                        if (customerName) {
                            customerName.classList.add('d-none');
                        }
                    } else {
                        if (customerName) {
                            customerName.classList.remove('d-none');
                        }
                    }

                    // Iterate over each order to display
                    orders.forEach((order, index) => {
                        const orderPrice = parseFloat(order.price.replace(/[^\d.-]/g,
                            '')); // Remove currency symbols
                        totalPrice += orderPrice * order.quantity; // Calculate total for each order

                        const drinks = ['Mountain Dew', 'Royal', 'Coke', 'Water', 'Sprite'];

                        const orderElement = `
                            <div class="order-item d-flex align-items-center justify-content-between border-bottom py-2">
                                <div class="order-details d-flex align-items-center">
                                    <img src="${order.image}" alt="${order.item}" class="img-fluid rounded" style="width: 50px; height: 50px;">
                                    <div class="ms-3">
                                        <div class="fw-bold">${order.item} (${order.quantity}x)</div>
                                        ${drinks.includes(order.item) ? '' : `<div class="text-muted">Sauce: ${order.sauce}</div>`}
                                        <div class="text-muted">Price: ${order.price}</div>
                                    </div>
                                </div>
                                <button class="btn btn-danger btn-sm" onclick="removeOrder(${index})">Remove</button>
                            </div>
                        `;

                        checkoutOffcanvasBody.insertAdjacentHTML('beforeend', orderElement);
                    });

                    // Format total price with commas if it reaches 1,000 or more
                    const formattedTotalPrice = totalPrice.toLocaleString('en-US');

                    // Display total price at the end
                    const totalElement = `
                        <div class="total-price fw-bold mt-2">
                            Total: &#8369;${formattedTotalPrice}
                        </div>
                    `;
                    checkoutOffcanvasBody.insertAdjacentHTML('beforeend', totalElement);
                }



                // Remove an order item from the checkout
                window.removeOrder = function(index) {
                    orders.splice(index, 1);
                    renderOrders();
                };

                function submitOrders() {
                    if (orders.length === 0) {
                        iziToast.warning({
                            title: 'Warning',
                            message: 'No orders added!',
                            position: 'topRight',
                            timeout: 3000,
                        });
                        return;
                    }

                    const customerName = document.getElementById('customerName').value.trim(); // Get customer name
                    const orderPayload = {
                        customer_name: customerName,
                        payment_method: orders[0].paymentMethod,
                        e_receipt: document.getElementById('eReceiptInput').files[0] ? document.getElementById(
                            'eReceiptInput').files[0] : null,
                    };

                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')); // CSRF token
                    // Add customer name and payment method to FormData
                    formData.append('customer_name', orderPayload.customer_name);
                    formData.append('payment_method', orderPayload.payment_method);

                    // Check if e_receipt is not null or undefined before appending
                    if (orderPayload.e_receipt) {
                        formData.append('e_receipt', orderPayload.e_receipt);
                    }

                    // Append orders to FormData
                    orders.forEach((order, index) => {
                        formData.append(`orders[${index}][item]`, order.item);
                        formData.append(`orders[${index}][quantity]`, order.quantity);
                        formData.append(`orders[${index}][sauce]`, order.sauce);
                        formData.append(`orders[${index}][image]`, order.image);
                        formData.append(`orders[${index}][price]`, order.price);
                    });

                    fetch('/send/cashier-order', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.message) {


                                document.querySelector('.order-body #totalPendingOrders').textContent =
                                    `${data.todayOrdersCount}`;
                                iziToast.success({
                                    title: 'Success',
                                    message: data.message,
                                    position: 'topRight',
                                    timeout: 3000,
                                });
                                orders = []; // Clear orders after successful submission
                                renderOrders();
                            } else {
                                iziToast.error({
                                    title: 'Error',
                                    message: 'Something went wrong!',
                                    position: 'topRight',
                                    timeout: 3000,
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            iziToast.error({
                                title: 'Error',
                                message: 'Could not connect to server!',
                                position: 'topRight',
                                timeout: 3000,
                            });
                        });
                }

                // Add event listener for the "Submit" button
                submitOrdersButton.addEventListener('click', function() {
                    const paymentOffcanvas = new bootstrap.Offcanvas(document.getElementById(
                        'offcanvasPayment'));
                    paymentOffcanvas.show();
                });
                // Add event listener for the "Submit Payment" button inside the payment offcanvas
                paymentSubmitButton.addEventListener('click', function() {
                    const fileNameDisplay = document.querySelector('.fileName');
                    const selectedPaymentMethod = document.querySelector(
                        'input[name="paymentMethod"]:checked').value;
                    const eReceiptInput = document.getElementById('eReceiptInput').files[0];

                    // Adjust e_receipt value based on payment method
                    let eReceiptValue = selectedPaymentMethod === 'Cash' ? null : eReceiptInput ?
                        eReceiptInput.name : null;

                    // Add payment details to the orders before submitting
                    orders = orders.map(order => ({
                        ...order,
                        paymentMethod: selectedPaymentMethod,
                        eReceipt: eReceiptValue // Store receipt file name if provided
                    }));
                    fileNameDisplay.textContent = '';
                    // Close the payment offcanvas
                    const paymentOffcanvasInstance = bootstrap.Offcanvas.getInstance(document
                        .getElementById('offcanvasPayment'));
                    paymentOffcanvasInstance.hide();

                    // Submit the order
                    submitOrders();
                });

                // Item selection setup for burgers and fries
                document.querySelectorAll('.burger-box, .fries-box, .drink-box').forEach((box) => {
                    box.addEventListener('click', function() {
                        const itemType = box.classList.contains('drink-box') ? 'drink' : box
                            .classList.contains('burger-box') ? 'burger' : 'fries';

                        document.getElementById('selected-item-title').textContent = box.dataset
                            .burger || box.dataset.fries;
                        document.getElementById('selected-item-img').src = box.querySelector('img')
                            .src;

                        const price = box.querySelector('.box-price span').textContent.trim();
                        document.querySelector('.box-price span').textContent = price;

                        if (itemType === 'drink') {
                            document.querySelector('.sauce-grid').style.display = 'none';
                            document.querySelector('.offcanvas-top').style.height = '55vh';
                        } else {
                            document.querySelector('.sauce-grid').style.display = 'flex';
                            document.querySelector('.offcanvas-top').style.height = '75vh';
                        }
                    });
                });

                // Sauce selection
                document.querySelectorAll('.sauce-box').forEach((box) => {
                    box.addEventListener('click', function() {
                        document.querySelectorAll('.sauce-box').forEach((sauce) => sauce.classList
                            .remove('selected'));
                        box.classList.add('selected');
                    });
                });

                // Quantity management
                const quantityInput = document.querySelector('.quantity__input');
                document.querySelector('.quantity__minus').addEventListener('click', function() {
                    let quantity = parseInt(quantityInput.value);
                    if (quantity > 1) quantityInput.value = --quantity;
                });

                document.querySelector('.quantity__plus').addEventListener('click', function() {
                    let quantity = parseInt(quantityInput.value);
                    quantityInput.value = ++quantity;
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const eReceiptInput = document.getElementById('eReceiptInput');
            const fileNameDisplay = document.querySelector('.fileName');
            const paymentSubmitButton = document.getElementById('paymentMethodSubmitButton');
            const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');

            // Check if the relevant elements exist on the page
            if (eReceiptInput && fileNameDisplay && paymentSubmitButton && paymentMethods.length > 0) {
                const orderExists = true; // Set this based on whether any orders exist

                // Enable/disable submit button based on conditions
                function updateSubmitButtonState() {
                    const isPaymentMethodSelected = Array.from(paymentMethods).some(method => method.checked);
                    const isReceiptUploaded = eReceiptInput.files.length > 0 ||
                        Array.from(paymentMethods).find(method => method.value === 'Cash');

                    paymentSubmitButton.disabled = !(orderExists && isPaymentMethodSelected && isReceiptUploaded);
                }

                // Handle file selection
                eReceiptInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        fileNameDisplay.textContent = `: ${this.files[0].name}`;
                    } else {
                        fileNameDisplay.textContent = '';
                    }
                    updateSubmitButtonState();
                });

                // Handle payment method changes
                paymentMethods.forEach(method => {
                    method.addEventListener('change', updateSubmitButtonState);
                });

                // Initially update button state
                updateSubmitButtonState();
            }
        });
        const offcanvasOrderStatus = document.querySelector('[data-bs-target="#offcanvasOrderStatus"]');
        if (offcanvasOrderStatus) {
            offcanvasOrderStatus.addEventListener('click', () => {
                loadPendingOrders();
            });
        }

        function updateOrderStatus(orderId, status, orderElement) {
            fetch(`/orders/update/${orderId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        status
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to update order status');
                    }
                    return response.json();
                })
                .then((data) => {
                    iziToast.success({
                        title: 'Success',
                        message: 'Order status updated successfully!',
                        position: 'topCenter',
                        timeout: 3000,
                    });
                    // Remove the entire accordion container (including customer name)
                    orderElement.parentElement.remove(); // This will remove the accordionContainer

                    // Update total sales display
                    if (data.totalSalesToday) {
                        document.querySelector('.total-sales').textContent = `₱ ${data.totalSalesToday}`;
                    } else {
                        console.warn('Total sales data is not available');
                    }

                    // Update total sales display
                    if (data.totalProfitToday) {
                        document.querySelector('.total-profit').textContent = `₱ ${data.totalProfitToday}`;
                    } else {
                        console.warn('Total profit data is not available');
                    }

                    // Update pending order count
                    const pendingCountElement = document.getElementById('totalPendingOrders');
                    if (pendingCountElement) {
                        pendingCountElement.textContent = parseInt(pendingCountElement.textContent) - 1;
                    } else {
                        console.warn('Pending Orders Count element not found');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    iziToast.error({
                        title: 'Error',
                        message: 'Something went wrong while updating the order status.',
                        position: 'topCenter',
                        timeout: 3000,
                    });
                });
        }

        function cancelOrder(orderGroupId, status, orderElement) {
            fetch(`/orders/cancel/${orderGroupId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to cancel order');
                    }
                    return response.json();
                })
                .then(data => {
                    iziToast.success({
                        title: 'Success',
                        message: data.message,
                        position: 'topCenter',
                        timeout: 3000,
                    });

                    // Remove the entire accordion container (including customer name)
                    orderElement.parentElement.remove(); // This will remove the accordionContainer

                    // Update pending order count
                    const pendingCountElement = document.getElementById('totalPendingOrders');
                    if (pendingCountElement) {
                        pendingCountElement.textContent = parseInt(pendingCountElement.textContent) - 1;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    iziToast.error({
                        title: 'Error',
                        message: 'Something went wrong while canceling the order.',
                        position: 'topCenter',
                        timeout: 3000,
                    });
                });
        }

        function loadPendingOrders() {
            fetch('/orders/pending')
                .then(response => response.json())
                .then(data => {
                    const ordersList = document.querySelector('.pending-orders-list');
                    ordersList.innerHTML = ''; // Clear the current list

                    if (data.length === 0) {
                        document.getElementById('noPendingOrderMessage').classList.remove('d-none');
                    } else {
                        document.getElementById('noPendingOrderMessage').classList.add('d-none');
                        pendingOrdersCount = data.length; // Update the pending orders count
                        updatePendingOrderCountUI(); // Update the UI count display

                        // Maintain order numbering based on the current list of pending orders
                        data.forEach((group, index) => {
                            // Accordion structure
                            // Create a container for the accordion
                            const accordionContainer = document.createElement('div');
                            accordionContainer.className =
                                'accordion-container'; // Create a container to hold everything

                            // Create customer name div
                            const customerNameDiv = document.createElement('div');
                            customerNameDiv.className = 'customer-name py-2';
                            customerNameDiv.innerHTML = `
    <span class="text-uppercase text-muted" style="font-size: 0.8rem;">Customer Name: <span class="text-success fw-bold">${group.customer_name}</span></span>
`;

                            // Append the customer name div to the accordion container
                            accordionContainer.appendChild(customerNameDiv);

                            // Now create the accordion structure as before
                            const accordion = document.createElement('div');
                            accordion.className = 'accordion mb-2';
                            accordion.id = `accordion${group.order_group_id}`;
                            accordion.style =
                                'background-color: #ffffff; border: 1px solid #ccc; z-index: 9999;';

                            // Add the accordion item
                            const accordionItem = document.createElement('div');
                            accordionItem.className = 'accordion-item';
                            accordionItem.style = 'border: none;';

                            // Set the header and body as before
                            const headerId = `heading${group.order_group_id}`;
                            const collapseId = `collapse${group.order_group_id}`;
                            const orderNumber = index + 1; // Adjust to 1-based index

                            // Check payment method for e-receipt link
                            const downloadLink = group.payment_method === 'Gcash' || group.payment_method ===
                                'PayMaya' ?
                                `<a href="/download-receipt/${group.order_group_id}" style="position: absolute; top: 10px; right: 10px;" class="ms-2" title="Download e-receipt">
            <i class="fa-solid fa-file-arrow-down text-danger" style="font-size: 1.5rem;"></i>
        </a>` :
                                `<span class="ms-2" style="color: grey; cursor: not-allowed; position: absolute; top: 10px; right: 10px;">
            <i class="fa-solid fa-file-arrow-down text-danger" style="font-size: 1.5rem; opacity: 0.5;"></i>
        </span>`;

                            // Accordion Header
                            accordionItem.innerHTML = `
    <h2 class="accordion-header d-flex align-items-center justify-content-between" id="${headerId}" style="position:relative;">
        <button class="accordion-button${index === 0 ? '' : ' collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${index === 0}" aria-controls="${collapseId}" style="color: #000; background-color: #ffffff; border: none;">
            <span style="font-size: 0.8rem;">Order #${orderNumber} - Payment Method: ${group.payment_method}</span>
        </button>
        <span class="badge bg-danger text-light ms-3" style="font-size: 0.8rem; position: absolute; top: 50%; transform: translateY(-50%); right: 15%; z-index: 800;">${group.status}</span>
    </h2>
`;

                            // Accordion Body
                            accordionItem.innerHTML += `
    <div id="${collapseId}" class="accordion-collapse collapse${index === 0 ? ' show' : ''}" aria-labelledby="${headerId}" data-bs-parent="#accordion${group.order_group_id}">
        <div class="accordion-body" style="background-color: #ffffff; position: relative;">
            ${downloadLink}
            <div class="items-sold mb-3">
                ${group.orders.map(order => `
                                                                                                                                                                                    <div class="order-item d-flex align-items-center justify-content-between border-bottom py-2">
                                                                                                                                                                                        <div class="order-details d-flex align-items-center">
                                                                                                                                                                                            <img src="${order.image}" alt="${order.item}" class="img-fluid rounded" style="width: 50px; height: 50px;">
                                                                                                                                                                                            <div class="ms-3">
                                                                                                                                                                                                <div class="fw-bold">${order.item} (${order.quantity}x)</div>
                                                                                                                                                                                                ${order.sauce && !['Mountain Dew', 'Royal', 'Coke', 'Water', 'Sprite'].includes(order.item) ? `
                                    <div class="text-muted">Sauce: ${order.sauce}</div>
                                ` : ''}
                                                                                                                                                                                                <div class="text-muted">Price: &#8369;${order.price}</div>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                `).join('')}
            </div>
            <!-- Status Update Button -->
          <div class="d-flex justify-content-end">
        <button class="btn btn-success me-2" onclick="updateOrderStatus(${group.order_group_id}, 'Served', document.getElementById('accordion${group.order_group_id}'))">Mark as Served</button>
        <button class="btn btn-danger" onclick="cancelOrder(${group.order_group_id}, 'Cancelled', document.getElementById('accordion${group.order_group_id}'))">Cancel Order</button>
    </div>

        </div>
    </div>
`;

                            // Append the accordion item to the accordion
                            accordion.appendChild(accordionItem);
                            accordionContainer.appendChild(accordion); // Append the accordion to the container

                            // Finally, append the accordion container to the orders list
                            ordersList.appendChild(accordionContainer);


                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading pending orders:', error);
                });
        }


        // Function to update the pending order count in the UI
        function updatePendingOrderCountUI() {
            const countElement = document.querySelector('#pendingOrderCount');
            if (countElement) {
                countElement.textContent = pendingOrdersCount;
            } else {
                console.warn('Pending Order Count element not found');
            }
        }
    </script>
</body>

</html>

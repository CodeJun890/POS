<?php $__env->startSection('page-title', 'City Burgers POS | Inventory Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid bg-white" id="orderHistory" style="overflow-x: hidden;">
        <div class="return p-3 text-dark">
            <a href="<?php echo e(route('manager-dashboard.get')); ?>" class="text-dark"><i class="ph ph-arrow-left me-1"></i></a>
        </div>
        <div class="header-title" style="left:29%;">
            <div class="fs-6 fw-bold">Inventory Management</div>
        </div>

        <div class="wrapper">
            <div class="searchBar">
                <input id="searchQueryInput" type="text" name="searchQueryInput" placeholder="Search" value="" />
                <button id="searchQuerySubmit" type="submit" name="searchQuerySubmit">
                    <!-- SVG Search Icon -->
                </button>
            </div>
        </div>
        <div class="mt-3 text-end">
            <div class="btn btn-dark btn-sm rounded-pill">
                <span class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createNewInventory">
                    <i class="ph ph-plus me-1"></i>New Item
                </span>
            </div>
        </div>
        <div class="container">
            <div class="lead mb-2 mt-3 d-flex align-items-center justify-content-center"><i class="ph ph-users-three me-1"
                    style="font-size: 1.5rem;"></i>Inventory Items
            </div>

            <?php if($inventories->isNotEmpty()): ?>
                <div class="inventory-grid py-3">
                    <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="inventory-card shadow-sm" style="position: relative;">
                            <div style="position: absolute; top: 5px; right: 5px;">
                                <i class="fa-solid fa-trash text-danger delete-inventory" style="font-size: 1.2rem;"
                                    data-id="<?php echo e($inventory->id); ?>"></i>
                            </div>
                            <div class="profile-picture mb-2">
                                <img src="<?php echo e($inventory->item_image ? '/storage/' . $inventory->item_image : asset('assets/images/empty-box.png')); ?>"
                                    class="rounded-circle img-fluid" alt="Manager Inventory" width="80" height="80">
                            </div>
                            <div class="inventory-info text-center">
                                <span class="fw-bold mb-1" style="font-size: 0.8rem;"><?php echo e($inventory->item_name); ?></span>
                                <div class="text-secondary text-capitalize" style="font-size: 0.8rem;">Quantity:
                                    <?php echo e($inventory->item_quantity); ?></div>
                                <div class="btn btn-dark btn-sm d-flex justify-content-center mt-2 update-inventory"
                                    data-id="<?php echo e($inventory->id); ?>">
                                    Update Item
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center mt-4 text-secondary">
                    <p>No inventory items found.</p>
                </div>
            <?php endif; ?>

        </div>
        <!--------- CREATE NEW INVENTORY MODAL ------------>
        <div class="modal fade" id="createNewInventory" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Create New Inventory</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-lg-12 d-flex justify-content-center align-items-center">
                                    <div class="item-image" style="position: relative;">
                                        <img src="<?php echo e(asset('assets/images/empty-box.png')); ?>"
                                            class="img-fluid rounded-circle border border-1" width="150px" height="150px"
                                            alt="">
                                        <label for="itemImage" id="cameraIcon"><i
                                                class="fa-solid fa-camera text-light"></i></label>
                                        <input type="file" id="itemImage" hidden>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="itemName" class="form-label fw-bold">Item Name</label>
                                        <input type="text" name="item_name" id="itemName" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form mb-2">
                                        <label for="itemQuantity" class="form-label fw-bold">Item Quantity</label>
                                        <!-- Use type="number" to restrict characters -->
                                        <input type="number" name="item_quantity" id="itemQuantity" class="form-control"
                                            min="1" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="createInventoryBtn" class="btn btn-success">Create</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Update Inventory Modal -->
        <div class="modal fade" id="updateInventoryModal" tabindex="-1" aria-labelledby="updateInventoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateInventoryModalLabel">Update Inventory Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateInventoryForm" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="modal-body">
                            <input type="hidden" id="updateItemId">
                            <div class="col-lg-12 d-flex justify-content-center align-items-center">
                                <div class="item-image" style="position: relative;">
                                    <img src="<?php echo e(asset('assets/images/empty-box.png')); ?>"
                                        class="img-fluid rounded-circle border border-1" id="updateItemImagePreview"
                                        width="150px" height="150px" alt="Item Image">
                                    <label for="updateItemImage" id="cameraIcon">
                                        <i class="fa-solid fa-camera text-light"></i>
                                    </label>
                                    <input type="file" name="item_image" id="updateItemImage" hidden>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="updateItemName" class="form-label">Item Name</label>
                                <input type="text" name="item_name" class="form-control" id="updateItemName">
                            </div>
                            <div class="mb-3">
                                <label for="updateItemQuantity" class="form-label">Quantity</label>
                                <input type="number" name="item_quantity" class="form-control" id="updateItemQuantity">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>




    </div>
    <!--- JQUERY CDN ---->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for click events on trash icons
            document.querySelectorAll('.delete-inventory').forEach((btn) => {
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
                        message: 'Do you really want to delete this inventory item?',
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
            function deleteCashier(inventoryId) {
                $.ajax({
                    url: '/inventory-management/delete/' + inventoryId,
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
                            document.querySelector(`[data-id="${inventoryId}"]`).closest(
                                    '.inventory-card')
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
                            message: 'Failed to delete inventory item. Please try again.',
                            position: 'topCenter'
                        });
                    }
                });
            }
        });

        // Restrict non-numeric input in the itemQuantity field
        document.getElementById('itemQuantity').addEventListener('keydown', function(event) {
            if (!((event.key >= '0' && event.key <= '9') || event.key === 'Backspace' || event.key ===
                    'ArrowLeft' || event.key === 'ArrowRight')) {
                event.preventDefault();
            }
        });


        document.getElementById('itemImage').addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const fileType = file.type;
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                const maxSize = 5 * 1024 * 1024; // 5 MB in bytes

                // Validate file size
                if (file.size > maxSize) {
                    iziToast.error({
                        title: 'Invalid File Size',
                        message: 'The file size must be 5MB or less!',
                        position: 'topRight',
                    });
                    this.value = ''; // Clear the file input
                    return;
                }

                // Validate file type
                if (!validTypes.includes(fileType)) {
                    iziToast.error({
                        title: 'Invalid File Type',
                        message: 'Only JPEG, PNG, JPG, and WEBP formats are allowed!',
                        position: 'topRight',
                    });
                    this.value = ''; // Clear the file input
                    return;
                }

                // Preview the selected image in the modal
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.item-image img').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle the Create Inventory button click
        document.getElementById('createInventoryBtn').addEventListener('click', function() {
            // Get form values
            const itemImage = document.getElementById('itemImage').files[0];
            const itemName = document.getElementById('itemName').value;
            const itemQuantity = document.getElementById('itemQuantity').value;

            // Validate fields
            if (!itemImage || !itemName || !itemQuantity) {
                iziToast.error({
                    title: 'Error',
                    message: 'All fields are required!',
                    position: 'topRight',
                });
                return;
            }

            // File size and type validation
            const fileType = itemImage.type;
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5 MB in bytes

            if (itemImage.size > maxSize) {
                iziToast.error({
                    title: 'Invalid File Size',
                    message: 'The file size must be 5MB or less!',
                    position: 'topRight',
                });
                return;
            }

            if (!validTypes.includes(fileType)) {
                iziToast.error({
                    title: 'Invalid File Type',
                    message: 'Only JPEG, PNG, JPG, and WEBP formats are allowed!',
                    position: 'topRight',
                });
                return;
            }

            // Create a FormData object for file upload
            let formData = new FormData();
            formData.append("_token", "<?php echo e(csrf_token()); ?>"); // Laravel CSRF token
            formData.append("item_name", itemName);
            formData.append("item_quantity", itemQuantity);
            formData.append("item_image", itemImage);

            // Send AJAX request to the server
            $.ajax({
                url: "<?php echo e(route('inventory-management.post')); ?>",
                type: 'POST',
                data: formData,
                processData: false, // Required for file uploads
                contentType: false, // Required for file uploads
                success: function(response) {
                    if (response.status === 'success') {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                        });

                        // Construct the new inventory card using response data
                        const newInventoryHTML = `
                    <div class="inventory-card shadow-sm" style="position: relative;">
                        <div style="position: absolute; top: 5px; right: 5px;">
                            <i class="fa-solid fa-trash text-danger delete-inventory" style="font-size: 1.2rem;" data-id="${response.inventory.id}"></i>
                        </div>
                        <div class="profile-picture mb-2">
                            <img src="${response.inventory.item_image ? '/storage/' + response.inventory.item_image : '<?php echo e(asset('assets/images/empty-box.png')); ?>'}"
                            class="rounded-circle img-fluid" alt="Inventory Image" width="80" height="80">
                        </div>
                        <div class="inventory-info text-center">
                            <span class="fw-bold mb-1" style="font-size: 0.8rem;">${response.inventory.item_name}</span>
                            <div class="text-secondary text-capitalize" style="font-size: 0.8rem;">Quantity: ${response.inventory.item_quantity}</div>
                            <div class="btn btn-dark btn-sm d-flex justify-content-center mt-2">Update Item</div>
                        </div>
                    </div>
                `;
                        document.querySelector('.inventory-grid').insertAdjacentHTML('beforeend',
                            newInventoryHTML);

                        // Clear the form fields
                        document.getElementById('itemImage').value = ''; // Clear file input
                        document.getElementById('itemName').value = ''; // Clear item name input
                        document.getElementById('itemQuantity').value = ''; // Clear item quantity input
                        document.querySelector('.item-image img').src =
                            "<?php echo e(asset('assets/images/empty-box.png')); ?>";

                        // Close the modal
                        $('#createNewInventory').modal('hide');
                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: response.message,
                            position: 'topRight'
                        });
                    }
                },
                error: function() {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to create inventory item. Please try again.',
                        position: 'topRight'
                    });
                }
            });
        });

        // Event listener for updating inventory
        document.querySelector('.inventory-grid').addEventListener('click', function(e) {
            if (e.target.classList.contains('update-inventory')) {
                const inventoryId = e.target.getAttribute('data-id');
                $.ajax({
                    url: `/inventory-management/show/${inventoryId}`,
                    type: 'GET',
                    success: function(response) {
                        document.getElementById('updateItemId').value = response.inventory.id;
                        document.getElementById('updateItemName').value = response.inventory.item_name;
                        document.getElementById('updateItemQuantity').value = response.inventory
                            .item_quantity;

                        const itemImage = response.inventory.item_image ?
                            `/storage/${response.inventory.item_image}` :
                            "<?php echo e(asset('assets/images/empty-box.png')); ?>";
                        document.getElementById('updateItemImagePreview').src = itemImage;

                        // Set the form action to include the inventory ID
                        const form = document.getElementById('updateInventoryForm');
                        form.action = `/inventory-management/update/${inventoryId}`;

                        $('#updateInventoryModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Error fetching inventory:', xhr);
                        iziToast.error({
                            title: 'Error',
                            message: 'Failed to fetch inventory item.',
                            position: 'topRight'
                        });
                    }
                });
            }
        });

        // Update image preview when a new image is selected
        document.getElementById('updateItemImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('updateItemImagePreview').src = e.target
                        .result; // Update the image preview
                };
                reader.readAsDataURL(file); // Read the file as a data URL
            }
        });
    </script>

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.manager_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Manager/manager_inventory_management.blade.php ENDPATH**/ ?>
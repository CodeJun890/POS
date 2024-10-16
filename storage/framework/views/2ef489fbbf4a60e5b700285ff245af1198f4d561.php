<?php $__env->startSection('page-title', 'City Burgers POS | Order History'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid bg-white" id="orderHistory" style="overflow-x: hidden;">
        <div class="return p-3 text-dark">
            <a href="<?php echo e(route('branch-management.view', encrypt($branch->id))); ?>" class="text-dark">
                <i class="ph ph-arrow-left me-1"></i>
            </a>
        </div>
        <div class="header-title">
            <div class="fs-6 fw-bold">Order History</div>
        </div>
        <div>
            <div class="wrapper">
                <div class="searchBar">
                    <input id="searchQueryInput" type="text" name="searchQueryInput" placeholder="Search"
                        value="" />
                    <button id="searchQuerySubmit" type="submit" name="searchQuerySubmit">
                        <!-- SVG Search Icon -->
                    </button>
                </div>
            </div>

            <div class="filter-categories">
                <div class="row flex-nowrap overflow-auto">
                    <a href="<?php echo e(route('manager-order-history-filter.get', ['filter' => 'all', 'branch_id' => encrypt($branch->id)])); ?>"
                        class="filter filter--all <?php echo e(request('filter') === 'all' || request('filter') === null ? 'active' : ''); ?>">
                        <i class="ph ph-squares-four me-1"></i> All
                    </a>
                    <a href="<?php echo e(route('manager-order-history-filter.get', ['filter' => 'today', 'branch_id' => encrypt($branch->id)])); ?>"
                        class="filter filter--today <?php echo e(request('filter') === 'today' ? 'active' : ''); ?>">
                        <i class="ph ph-calendar-today me-1"></i> Today
                    </a>
                    <a href="<?php echo e(route('manager-order-history-filter.get', ['filter' => 'yesterday', 'branch_id' => encrypt($branch->id)])); ?>"
                        class="filter filter--yesterday <?php echo e(request('filter') === 'yesterday' ? 'active' : ''); ?>">
                        <i class="ph ph-clock-counter-clockwise me-1"></i> Yesterday
                    </a>
                    <a href="<?php echo e(route('manager-order-history-filter.get', ['filter' => 'this-month', 'branch_id' => encrypt($branch->id)])); ?>"
                        class="filter filter--this-month <?php echo e(request('filter') === 'this-month' ? 'active' : ''); ?>">
                        <i class="ph ph-calendar-blank me-1"></i> This Month
                    </a>
                    <a href="<?php echo e(route('manager-order-history-filter.get', ['filter' => 'this-year', 'branch_id' => encrypt($branch->id)])); ?>"
                        class="filter filter--this-year <?php echo e(request('filter') === 'this-year' ? 'active' : ''); ?>">
                        <i class="ph ph-calendar me-1"></i> This Year
                    </a>
                </div>
            </div>

            <div class="row order-summary mt-4 px-3" id="orderSummaryContainer">
                <?php if(isset($orderSummary) && $orderSummary->isNotEmpty()): ?>
                    <?php $__currentLoopData = $orderSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div
                            class="col-lg-12 rounded shadow d-flex align-items-center p-2 border border-dark mt-2 order-item">
                            <div class="col-2 text-center mt-2">
                                <i class="ph ph-receipt"></i>
                            </div>
                            <div class="col-10 flex-1">
                                <p class="date fw-bold" style="font-size: 0.8rem;">
                                    <?php echo e(Carbon\Carbon::parse($summary['date'])->format('l, F j, Y')); ?>

                                </p>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <p class="sales">Total Sales: &#8369; <?php echo e(number_format($summary['total_sales'], 2)); ?>

                                    </p>
                                    <p class="profit border-3 border-start border-success ps-2">Total Profit: &#8369;
                                        <?php echo e(number_format($summary['total_profit'], 2)); ?></p>
                                </div>
                                <div class="check-order d-flex mt-1">
                                    <button
                                        class="d-flex align-items-center py-1 px-2 bg-primary rounded-2 text-light border-0 view-receipt-btn"
                                        data-branch-id="<?php echo e($branch->id); ?>"
                                        data-date="<?php echo e(Carbon\Carbon::parse($summary['date'])->format('Y-m-d')); ?>"
                                        style="font-size: 0.6rem;">
                                        View Receipt <i class="ph ph-eye ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="mt-4 d-flex align-items-center justify-content-center" style="font-size: 0.8rem;">
                        <i class="ph ph-x-circle me-1" style="font-size: 1.25rem;"></i> No orders found for this filter.
                    </div>
                <?php endif; ?>
                <div class="d-none mt-4 d-flex align-items-center justify-content-center" id="noMatchesMessage"
                    style="font-size: 0.8rem;">
                    <i class="ph ph-x-circle me-1" style="font-size: 1.25rem;"></i> No matches found.
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewReceiptButtons = document.querySelectorAll('.view-receipt-btn');

            viewReceiptButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const date = this.getAttribute(
                        'data-date'); // Retrieve the date from the data attribute
                    const branchId = this.getAttribute(
                        'data-branch-id'); // Retrieve the branch ID from the data attribute

                    const encodedDate = encodeURIComponent(
                        date); // Encode the date to be safe for URL parameters
                    const encodedBranchId = encodeURIComponent(
                        branchId); // Encode the branch ID to be safe for URL parameters

                    console.log(
                        `Redirecting to: /manager-receipt/view?date=${encodedDate}&branch_id=${encodedBranchId}`
                    ); // Debugging
                    // Redirect to a specific route with the date and branch ID as parameters
                    window.location.href =
                        `/manager-receipt/view?date=${encodedDate}&branch_id=${encodedBranchId}`;
                });
            });
        });

        document.getElementById('searchQueryInput').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim(); // Get the trimmed query
            const orderItems = document.querySelectorAll('.order-item');
            const noMatchesMessage = document.getElementById('noMatchesMessage');

            // Split query into words and filter out empty entries
            const queryWords = query.split(' ').filter(word => word.length > 0);
            let matchFound = false; // Flag to track if any match is found

            orderItems.forEach(order => {
                const dateText = order.querySelector('.date').textContent.toLowerCase()
                    .trim(); // Get date text and trim it
                const [dayName, month, day, year] = dateText.split(
                    /[,\s]+/); // Split on comma and whitespace

                // Create variations for partial matches
                const formattedDay = day.replace(/^0+/, ''); // Remove any leading zeroes from the day
                const partialDate1 = `${month} ${formattedDay}, ${year}`
                    .toLowerCase(); // e.g., "September 28, 2024"
                const partialDate2 = `${formattedDay} ${month} ${year}`
                    .toLowerCase(); // e.g., "28 September 2024"
                const partialDate3 = `${formattedDay} ${month}, ${year}`
                    .toLowerCase(); // e.g., "28 September, 2024"
                const fullDate = `${dayName}, ${month} ${formattedDay}, ${year}`
                    .toLowerCase(); // Full formatted date

                // Join various combinations for date variations
                const dateVariations = [fullDate, partialDate1, partialDate2, partialDate3, dayName, month,
                    formattedDay, year
                ].join(' ');

                // Check if the entire query matches any part of the date variations
                const isMatch = dateVariations.includes(query);

                if (query === '' || isMatch) {
                    order.classList.remove('d-none'); // Show order by removing d-none class
                    matchFound = true; // At least one match is found
                } else {
                    order.classList.add('d-none'); // Hide order by adding d-none class
                }
            });

            // Show or hide the no matches message
            if (!matchFound) {
                noMatchesMessage.classList.remove('d-none'); // Show message
            } else {
                noMatchesMessage.classList.add('d-none'); // Hide message
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.manager_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Manager/manager_order_history.blade.php ENDPATH**/ ?>
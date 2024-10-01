@extends('Layout.cashier_layout')

@section('page-title', 'City Burgers POS | Order History')

@section('content')
    <div class="container-fluid bg-white" id="orderHistory" style="overflow-x: hidden;">
        <div class="return p-3 text-dark">
            <a href="{{ route('cashier-order.get') }}" class="text-dark"><i class="ph ph-arrow-left me-1"></i></a>
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
                    <a href="{{ route('cashier-order-history-filter.get', ['filter' => 'all']) }}"
                        class="filter filter--all {{ request('filter') === 'all' || request('filter') === null ? 'active' : '' }}">
                        <i class="ph ph-squares-four me-1"></i> All
                    </a>
                    <a href="{{ route('cashier-order-history-filter.get', ['filter' => 'today']) }}"
                        class="filter filter--today {{ request('filter') === 'today' ? 'active' : '' }}">
                        <i class="ph ph-calendar-today me-1"></i> Today
                    </a>
                    <a href="{{ route('cashier-order-history-filter.get', ['filter' => 'yesterday']) }}"
                        class="filter filter--yesterday {{ request('filter') === 'yesterday' ? 'active' : '' }}">
                        <i class="ph ph-clock-counter-clockwise me-1"></i> Yesterday
                    </a>
                    <a href="{{ route('cashier-order-history-filter.get', ['filter' => 'this-month']) }}"
                        class="filter filter--this-month {{ request('filter') === 'this-month' ? 'active' : '' }}">
                        <i class="ph ph-calendar-blank me-1"></i> This Month
                    </a>
                    <a href="{{ route('cashier-order-history-filter.get', ['filter' => 'this-year']) }}"
                        class="filter filter--this-year {{ request('filter') === 'this-year' ? 'active' : '' }}">
                        <i class="ph ph-calendar me-1"></i> This Year
                    </a>
                </div>
            </div>

            <div class="row order-summary mt-4 px-3" id="orderSummaryContainer">
                @if (isset($orderSummary) && $orderSummary->isNotEmpty())
                    @foreach ($orderSummary as $summary)
                        <div
                            class="col-lg-12 rounded shadow d-flex align-items-center p-2 border border-dark mt-2 order-item">
                            <div class="col-2 text-center mt-2">
                                <i class="ph ph-receipt"></i>
                            </div>
                            <div class="col-10 flex-1">
                                <p class="date fw-bold" style="font-size: 0.8rem;">
                                    {{ Carbon\Carbon::parse($summary['date'])->format('l, F j, Y') }}
                                </p>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <p class="sales">Total Sales: &#8369; {{ number_format($summary['total_sales'], 2) }}
                                    </p>
                                    <p class="profit border-3 border-start border-success ps-2">Total Profit: &#8369;
                                        {{ number_format($summary['total_profit'], 2) }}</p>
                                </div>
                                <div class="check-order d-flex mt-1">
                                    <button
                                        class="d-flex align-items-center py-1 px-2 bg-primary rounded-2 text-light border-0 view-receipt-btn"
                                        data-date="{{ Carbon\Carbon::parse($summary['date'])->format('Y-m-d') }}"
                                        style="font-size: 0.6rem;">
                                        View Receipt <i class="ph ph-eye ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="mt-4 d-flex align-items-center justify-content-center" style="font-size: 0.8rem;">
                        <i class="ph ph-x-circle me-1" style="font-size: 1.25rem;"></i> No orders found for this filter.
                    </div>
                @endif
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
                    const encodedDate = encodeURIComponent(
                        date); // Encode the date to be safe for URL parameters

                    console.log(`Redirecting to: /receipt/view?date=${encodedDate}`); // Debugging
                    // Redirect to a specific route with the date as a parameter
                    window.location.href = `/receipt/view?date=${encodedDate}`;
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


@endsection

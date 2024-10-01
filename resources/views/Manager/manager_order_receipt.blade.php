@extends('Layout.manager_layout')

@section('page-title', 'City Burgers POS | Order Receipt')

@section('content')
    <div class="container-fluid bg-white" style="overflow-x: hidden; " id="orderHistory">
        <div class="return p-3 text-dark">
            <a href="{{ route('manager-order-history.get') }}" class="text-dark"><i class="ph ph-arrow-left me-1"></i></a>
        </div>
        <div class="header-title">
            <div class="fs-6 fw-bold">Order Receipt</div>
        </div>
        <div class="d-flex justify-content-center align-items-center ">
            <div class="order-calculate text-center bg-light p-2 w-100 rounded-4 border border-1 border-secondary">
                <div class="d-flex justify-content-around">
                    <div class="text-center">
                        <div class="fs-6 fw-bold text-uppercase">Total</div>
                        <p class="m-0 total-sales">&#8369; {{ number_format($totalSales, 2) }}</p>
                    </div>

                    <div class="text-center">
                        <div class="fs-6 fw-bold text-uppercase">Profit</div>
                        <p class="m-0">&#8369; {{ number_format($totalProfit, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div>

            <div class="order-group">
                <div class="fw-bold text-uppercase mt-4 mb-3">Orders on: <span
                        class="fw-normal text-capitalize ms-2">{{ $formattedDate }}</span></div>

                @php
                    $counter = 1; // Initialize a counter
                @endphp

                @foreach ($orderGroups as $group)
                    <div class="accordion mb-2" id="accordion{{ $group->id }}"
                        style="background-color: #ffffff; border: 1px solid #ccc;">
                        <div class="accordion-item" style="border: none;">
                            <h2 class="accordion-header" id="heading{{ $group->id }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $group->id }}" aria-expanded="true"
                                    aria-controls="collapse{{ $group->id }}"
                                    style="color: #000; background-color: #ffffff; border: none;">
                                    Order #{{ $counter++ }} - Payment Method: {{ $group->payment_method }}
                                </button>
                            </h2>
                            <div id="collapse{{ $group->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $group->id }}"
                                data-bs-parent="#accordion{{ $group->id }}">
                                <div class="accordion-body" style="background-color: #ffffff; position: relative;">
                                    @if ($group->payment_method === 'Gcash' || $group->payment_method === 'PayMaya')
                                        <a href="{{ route('download.receipt', $group->id) }}"
                                            style="position: absolute; top: 10px; right: 10px;" class="ms-2"
                                            title="Download e-receipt">
                                            <i class="fa-solid fa-file-arrow-down text-danger"
                                                style="font-size: 1.5rem;"></i>
                                        </a>
                                    @else
                                        <span class="ms-2"
                                            style="color: grey; cursor: not-allowed; position: absolute; top: 10px; right: 10px;">
                                            <i class="fa-solid fa-file-arrow-down text-danger"
                                                style="font-size: 1.5rem; opacity: 0.5;"></i>
                                        </span>
                                    @endif
                                    <div class="items-sold">
                                        @foreach ($group->orders as $order)
                                            <div
                                                class="order-item d-flex align-items-center justify-content-between border-bottom py-2">
                                                <div class="order-details d-flex align-items-center">
                                                    <img src="{{ $order->image }}" alt="{{ $order->item }}"
                                                        class="img-fluid rounded" style="width: 50px; height: 50px;">
                                                    <div class="ms-3">
                                                        <div class="fw-bold">{{ $order->item }} ({{ $order->quantity }}x)
                                                        </div>
                                                        @php
                                                            $drinks = [
                                                                'Mountain Dew',
                                                                'Royal',
                                                                'Coke',
                                                                'Water',
                                                                'Sprite',
                                                            ];
                                                        @endphp
                                                        @if (!in_array($order->item, $drinks) && $order->sauce)
                                                            <div class="text-muted">Sauce: {{ $order->sauce }}</div>
                                                        @endif
                                                        <div class="text-muted">Price:
                                                            &#8369;{{ number_format($order->price, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>



        </div>
    </div>

    <script>
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', () => {
                // Toggle the 'collapsed' class on the button
                button.classList.toggle('collapsed');
            });
        });
    </script>


@endsection

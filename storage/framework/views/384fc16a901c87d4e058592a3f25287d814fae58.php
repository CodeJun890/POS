<?php $__env->startSection('page-title', 'City Burgers POS | Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-0" id="dashboardContainer">
        <div class="dashboard-header p-3">
            <?php echo $__env->make('Partial.manager_navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="lead fw-bold text-white">Dashboard</div>

            <div class="container bg-light rounded-3 p-3 mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-uppercase fw-bold text-dark">Total Sales</div>
                    <div class="text-uppercase fw-bold text-dark"><?php echo e(now()->format('Y')); ?></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="fs-1">&#8369;<?php echo e(number_format($totalSales, 2)); ?></span>
                    <i class="fa-solid fa-shopping-cart" style="font-size: 2rem; color: #ec3001;"></i>
                </div>
            </div>
            <div class="container bg-light rounded-3 p-3 mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-uppercase fw-bold text-dark">Total Profit</div>
                    <div class="text-uppercase fw-bold text-dark"><?php echo e(now()->format('Y')); ?></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="fs-1">&#8369;<?php echo e(number_format($totalProfit, 2)); ?></span>
                    <i class="fa-solid fa-comments-dollar text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="dashboard-body bg-light dashboard-body-manager px-3 py-2" style="overflow-x: hidden;">

            <div class="fs-6 text-uppercase fw-bold text-dark"><i class="fa-solid fa-ranking-star me-2"
                    style=" color: #ffd700; "></i>Top Selling Food
            </div>
            <div class="dashboard-inner-body">
                <?php $__currentLoopData = $trendingFood; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(is_object($food)): ?>
                        <div class="order-item d-flex align-items-center border-bottom py-2 position-relative">
                            <div class="order-details d-flex align-items-center">
                                <img src="<?php echo e(asset($food->image)); ?>" alt="<?php echo e($food->item); ?>" class="img-fluid rounded"
                                    style="width: 50px; height: 50px;">
                                <div class="ms-3 text-dark" style="position: relative;">
                                    <div class="fw-bold"><?php echo e($food->item); ?></div>
                                    <div class="text-dark">Sauce: <?php echo e($food->sauce); ?></div>
                                    <div class="text-dark">Price: &#8369;<?php echo e($food->price); ?></div>
                                </div>
                            </div>
                            <div class="fw-bold bg-danger text-light rounded-5 px-2 py-1"
                                style="position: absolute; top:5px; right:0;">
                                <span><i class="fa-solid fa-trophy"
                                        style="color: <?php echo e($index === 0 ? '#ffd700' : 'rgb(219, 219, 219)'); ?>;"></i> Top
                                    <?php echo e($index + 1); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-center my-4">No data yet.</p>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
            <div class="sauce-graph mt-3">
                <div class="fs-6 text-uppercase fw-bold text-light"><i class="fa-solid fa-chart-pie me-2"
                        style="color: #ffd700;"></i>Sauce Requests Overview</div>
                <canvas id="sauceTrendChart" width="400" height="200"></canvas>
            </div>
            <div class="weekly-sales-graph ">
                <div class="fs-6 text-uppercase fw-bold text-light mb-3">
                    <i class="ph ph-chart-bar me-2" style="color: #ffd700;"></i>
                    Weekly Sales and Profit
                </div>
                <div class="d-flex flex-column mb-3">
                    <input type="date" id="weekPicker" class="form-control me-2" hidden />

                    <div class="d-flex justify-content-evenly gap-2">
                        <div class="text-light d-flex align-items-center justify-content-center gap-1">
                            <i id="prevWeek" class="ph ph-arrow-circle-left" style="font-size: 1.2rem;"></i>
                        </div>
                        <div class="date-range text-light fw-bold" id="dateRangeDisplay" style="font-size: 0.8rem;"></div>
                        <div class="text-light d-flex align-items-center justify-content-center gap-1">
                            <i id="nextWeek" class="ph ph-arrow-circle-right" style="font-size: 1.2rem;"></i>
                        </div>
                    </div>
                </div>
                <canvas id="weeklySalesChart" class="mt-3"></canvas>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = <?php echo json_encode($chartData, 15, 512) ?>; // Pass the data from the controller

            // Check if chartData exists and has data
            if (chartData && chartData.labels.length > 0 && chartData.data.length > 0) {
                // Create the pie chart
                const ctx = document.getElementById('sauceTrendChart').getContext('2d');
                const sauceTrendChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Total Sauce Request',
                            data: chartData.data,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top', // Change this to 'bottom', 'left', or 'right' if needed
                                align: 'center', // Align legend items
                                labels: {
                                    boxWidth: 20, // Width of the color boxes
                                    padding: 10, // Space between legend items
                                    color: 'white' // Change the legend text color to white
                                }
                            },
                            title: {
                                display: true,
                                text: 'Customer Preferred Sauce',
                                color: 'white' // Change title text color to white
                            }
                        }
                    }
                });
            } else {
                console.warn('No data available for the sauce trend chart.');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Data from the backend
            const labels = <?php echo json_encode($weeklyChartData['labels'], 15, 512) ?>; // This should be ["Monday", "Tuesday", ...]
            const salesData = <?php echo json_encode($weeklyChartData['sales'], 15, 512) ?>;
            const profitData = <?php echo json_encode($weeklyChartData['profit'], 15, 512) ?>;

            const ctx = document.getElementById('weeklySalesChart').getContext('2d');
            const weeklySalesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels, // Use labels directly
                    datasets: [{
                        label: 'Sales',
                        data: salesData,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Profit',
                        data: profitData,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount (₱)',
                                color: 'white',
                                font: {
                                    size: 10
                                }
                            },
                            ticks: {
                                color: 'white',
                                font: {
                                    size: 6
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Day',
                                color: 'white',
                                font: {
                                    size: 10
                                }
                            },
                            ticks: {
                                color: 'white',
                                font: {
                                    size: 6
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: 'white',
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });

            let currentDate = new Date();
            currentDate.setDate(currentDate.getDate() - currentDate.getDay() + 1); // Adjust to Monday

            function formatDateRange(startDate) {
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 6);
                const options = {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                };
                return `${startDate.toLocaleDateString('en-US', options)} - ${endDate.toLocaleDateString('en-US', options)}`;
            }

            function updateChartData(weekOffset) {
                const newDate = new Date(currentDate);
                newDate.setDate(currentDate.getDate() + (weekOffset * 7));
                newDate.setDate(newDate.getDate() - newDate.getDay() + 1); // Adjust to Monday
                const formattedDate = newDate.toISOString().split('T')[0];

                fetch(`/get-weekly-data?date=${formattedDate}`)
                    .then(response => response.json())
                    .then(data => {
                        weeklySalesChart.data.labels = labels; // Keep labels consistent with the PHP side
                        weeklySalesChart.data.datasets[0].data = labels.map(day => data.sales[day] || 0);
                        weeklySalesChart.data.datasets[1].data = labels.map(day => data.profit[day] || 0);
                        weeklySalesChart.update();

                        document.getElementById('dateRangeDisplay').innerText = formatDateRange(newDate);
                        currentDate = newDate;
                    });
            }

            document.getElementById('dateRangeDisplay').innerText = formatDateRange(currentDate);
            document.getElementById('prevWeek').addEventListener('click', () => {
                updateChartData(-1);
            });
            document.getElementById('nextWeek').addEventListener('click', () => {
                updateChartData(1);
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layout.manager_layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\city_burgers_pos_system\resources\views/Manager/manager_dashboard.blade.php ENDPATH**/ ?>
<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CashierController extends Controller
{
    public function viewCashierDashboard() {
        $user = Auth::user();
        $branch = Branch::findOrFail($user->branch_id);
        if ($user) {
            $drinks = ['Mountain Dew', 'Royal', 'Coke', 'Water (CvSU)', 'Sprite', 'Water'];

            // Fetch the top 3 trending food items
            $trendingFood = DB::table('orders')
                ->select('item', 'sauce', 'image', DB::raw('count(*) as total'))
                ->whereNotIn('item', $drinks)
                ->groupBy('item', 'sauce', 'image')
                ->orderBy('total', 'desc')
                ->limit(2)
                ->get();

            // Fetch the top trending drink
            $trendingDrink = DB::table('orders')
                ->select('item', 'image', DB::raw('count(*) as total'))
                ->whereIn('item', $drinks)
                ->groupBy('item', 'image')
                ->orderBy('total', 'desc')
                ->limit(1)
                ->first();

            // Return the view with the data
            return view('Cashier.cashier_dashboard', compact('user', 'trendingFood', 'trendingDrink' ,'branch'));
        }
        return redirect()->route('login.get');
    }
    public function viewCashierOrder() {
        $user = Auth::user();
        $branch = Branch::findOrFail($user->branch_id);
        if ($user) {
            // Retrieve the cashier's branch_id
            $branchId = $user->branch_id;

            if (!$branchId) {
                return redirect()->back()->with('error', 'You are not assigned to any branch.');
            }

            // Only sum up the total sales for the cashier's branch where the order group is "Served"
            $totalSalesToday = Order::whereDate('created_at', today())
                ->where('branch_id', $branchId) // Filter by cashier's branch
                ->whereHas('orderGroup', function ($query) {
                    $query->where('status', 'Served');
                })
                ->sum(DB::raw('price * quantity'));

            // Calculate total profit for today for the cashier's branch where the order group is "Served"
            $totalProfitToday = Order::whereDate('created_at', today())
                ->where('branch_id', $branchId) // Filter by cashier's branch
                ->whereHas('orderGroup', function ($query) {
                    $query->where('status', 'Served');
                })
                ->sum(DB::raw('profit * quantity'));

            // Count the number of unique order groups with status 'Pending' for the cashier's branch
            $todayOrdersCount = OrderGroup::whereDate('created_at', today())
                ->where('branch_id', $branchId) // Filter by cashier's branch
                ->where('status', 'Pending')
                ->count();


            return view('Cashier.cashier_order', compact('user', 'totalSalesToday', 'totalProfitToday', 'todayOrdersCount', 'branch'));
        }

        return redirect()->route('login.get');
    }
    public function postCashierOrder(Request $request) {
        // Log the incoming request data
        Log::info('Incoming request for postCashierOrder:', $request->all());

        try {
            // Validate the incoming request
            $validated = $request->validate([
                'customer_name' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:255',
                'e_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Adjust max size as needed
                'orders' => 'required|array',
                'orders.*.item' => 'required|string|max:255',
                'orders.*.quantity' => 'required|integer|min:1',
                'orders.*.sauce' => 'nullable|string|max:255',
                'orders.*.image' => 'required|string|max:255',
                'orders.*.price' => 'required|string|max:255',
            ]);

            // Step 1: Handle e_receipt file upload
            $receiptPath = null;
            if ($request->hasFile('e_receipt')) {
                $receiptPath = $request->file('e_receipt')->store('receipts', 'public'); // Store the e-receipt
            }

            // Step 2: Get cashier's branch_id from authenticated user (assuming auth user is a cashier)
            $branchId = Auth::user()->branch_id;


            if (!$branchId) {
                return response()->json(['message' => 'Cashier is not assigned to any branch.'], 400);
            }

            // Step 3: Create the Order Group
            $customerName = $validated['customer_name'] ?? 'N/A';

            $orderGroup = OrderGroup::create([
                'payment_method' => $validated['payment_method'],
                'customer_name' => $customerName,
                'e_receipt' => $receiptPath, // Store the path if available
                'branch_id' => $branchId, // Associate with the branch
            ]);


            // Step 4: Attach each order item to the created Order Group and assign the branch
            foreach ($validated['orders'] as $item) {
                // Create the Order and set profit automatically
                Order::create([
                    'item' => $item['item'],
                    'quantity' => $item['quantity'],
                    'sauce' => $item['sauce'],
                    'image' => $item['image'],
                    'price' => floatval(str_replace(['₱', ' '], '', $item['price'])), // Convert price to float
                    'order_group_id' => $orderGroup->id, // Attach to the order group
                    'branch_id' => $branchId, // Associate each order with the branch
                ]);
            }

            // Step 5: Count the number of unique order groups with status 'Pending' for the branch
            $todayOrdersCount = OrderGroup::whereDate('created_at', today())
                ->where('status', 'Pending')
                ->where('branch_id', $branchId) // Filter by branch
                ->count();

            return response()->json([
                'message' => 'Order submitted successfully!',
                'todayOrdersCount' => $todayOrdersCount,
            ], 201);

        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error in postCashierOrder: ' . $e->getMessage(), [
                'request' => $request->all(),
                'stack' => $e->getTraceAsString(),
            ]);

            // Return an error response
            return response()->json(['message' => 'An error occurred while processing your order.'], 500);
        }
    }
    public function getPendingOrders() {
        // Get the current user (cashier) and their branch_id
        $user = Auth::user();
        $branchId = $user->branch_id;

        // Get today's pending orders for the cashier's branch, grouped by order_group_id
        $todayOrders = Order::whereDate('created_at', today())
                            ->whereHas('orderGroup', function($query) use ($branchId) {
                                $query->where('branch_id', $branchId) // Filter by branch_id in the order group
                                      ->where('status', 'Pending'); // Only include pending order groups
                            })
                            ->with('orderGroup') // Eager load the related order group
                            ->get()
                            ->groupBy('order_group_id');

        // Prepare a structured response
        $response = [];
        foreach ($todayOrders as $groupId => $orders) {
            $orderGroup = $orders->first()->orderGroup; // Get the first order's group details

            // Only include groups that are still pending
            if ($orderGroup && $orderGroup->status === 'Pending') {
                $response[] = [
                    'order_group_id' => $groupId,
                    'status' => $orderGroup->status,
                    'customer_name' => $orderGroup->customer_name,
                    'payment_method' => $orderGroup->payment_method, // Include payment method from orderGroup
                    'orders' => $orders
                ];
            }
        }

        // Check if there are no pending orders
        if (empty($response)) {
            return response()->json([
                'message' => 'No pending orders found for today.',
                'data' => []
            ]);
        }

        return response()->json($response);
    }

    public function updateGroupStatus($orderGroupId) {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $orderGroup = OrderGroup::find($orderGroupId);

        if ($orderGroup) {
            // Update the order group status first
            $orderGroup->status = 'Served';
            $orderGroup->save();

            // Now calculate today's total sales where the order group is "Served" and belongs to the user's branch
            $updatedTotalSales = Order::whereDate('created_at', today())
                ->where('branch_id', $user->branch_id) // Filter by branch_id
                ->whereHas('orderGroup', function ($query) {
                    $query->where('status', 'Served');
                })
                ->sum(DB::raw('price * quantity'));

            // Calculate today's total profit where the order group is "Served" and belongs to the user's branch
            $updatedTotalProfit = Order::whereDate('created_at', today())
                ->where('branch_id', $user->branch_id) // Filter by branch_id
                ->whereHas('orderGroup', function ($query) {
                    $query->where('status', 'Served');
                })
                ->sum(DB::raw('profit * quantity'));

            return response()->json([
                'message' => 'Order group status updated!',
                'totalSalesToday' => number_format($updatedTotalSales, 2),
                'totalProfitToday' => number_format($updatedTotalProfit, 2), // Include total profit
            ], 200);
        }

        return response()->json(['message' => 'Order group not found!'], 404);
    }

    public function cancelOrder($orderGroupId) {
        // Retrieve the order group
        $orderGroup = OrderGroup::with('orders')->find($orderGroupId);

        if (!$orderGroup) {
            return response()->json(['message' => 'Order group not found'], 404);
        }

        // Check if the payment method is not cash
        if ($orderGroup->payment_method !== 'Cash') {
            // Check if the e-receipt file exists in storage
            if (Storage::exists($orderGroup->e_receipt)) {
                // Delete the e-receipt file
                Storage::delete($orderGroup->e_receipt);
            } else {
                // Optionally log or handle the case where the e-receipt does not exist
                Log::warning("E-receipt does not exist: {$orderGroup->e_receipt}");
            }
        }


        // Delete all associated orders
        $orderGroup->orders()->delete(); // Deletes all orders related to the order group

        // Delete the order group
        $orderGroup->delete();

        return response()->json(['message' => 'Order and associated orders have been canceled and deleted successfully!'], 200);
    }
    public function viewCashierOrderHistory() {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.get');
        }

        // Retrieve all orders that belong to 'Served' order groups and match the cashier's branch_id
        $orders = Order::whereHas('orderGroup', function ($query) {
            $query->where('status', 'Served');
        })->where('branch_id', $user->branch_id) // Filter by the cashier's branch_id
        ->get();

        // Group by date and calculate total sales and profit for each day
        $orderSummary = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('l, F j, Y'); // Format to 'Day, Month Day, Year'
        })->map(function ($group) {
            return [
                'total_sales' => $group->sum(function ($order) {
                    return $order->price * $order->quantity; // Calculate total sales for each day
                }),
                'total_profit' => $group->sum(function ($order) {
                    return $order->profit * $order->quantity; // Calculate total profit for each day using actual profit values
                }),
                'date' => $group->first()->created_at, // Store the date from the first order in the group
                'orders' => $group // Optionally, store the orders if you need to access them later
            ];
        });

        // Sort the orderSummary by date in descending order
        $orderSummary = $orderSummary->sortByDesc('date');

        return view('Cashier.cashier_order_history', compact('user', 'orderSummary'));
    }
    public function filterCashierOrderHistory(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.get');
        }

        // Define date ranges for various filters
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();
        $thisYearStart = now()->startOfYear();
        $thisYearEnd = now()->endOfYear();

        // Determine the selected filter
        $filter = $request->input('filter', 'all');

        // Initialize the query builder for orders
        $query = Order::where('branch_id', $user->branch_id); // Filter by branch_id

        switch ($filter) {
            case 'today':
                $orders = $query->whereDate('created_at', $today)->get();
                $dateLabel = $today->format('Y-m-d');
                break;
            case 'yesterday':
                $orders = $query->whereDate('created_at', $yesterday)->get();
                $dateLabel = $yesterday->format('Y-m-d');
                break;
            case 'this-month':
                $orders = $query->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->get();
                $dateLabel = now()->format('F Y'); // This month label (e.g., September 2024)
                break;
            case 'this-year':
                $orders = $query->whereBetween('created_at', [$thisYearStart, $thisYearEnd])->get();
                $dateLabel = now()->format('Y'); // This year label (e.g., 2024)
                break;
            default:
                // Handle case for 'all' or other defaults
                $orders = $query->get();
                $dateLabel = 'All Time';
                break;
        }

        // Group the orders by date and calculate total sales and profit
        $orderSummary = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d'); // Group orders by date
        })->map(function ($orders, $date) {
            $totalSales = $orders->sum(function ($order) {
                return $order->price * $order->quantity;
            });

            $totalProfit = $totalSales * 0.2; // Example: 20% profit margin

            return [
                'date' => $date,
                'total_sales' => $totalSales,
                'total_profit' => $totalProfit,
            ];
        });

        // Pass the data to the view
        return view('Cashier.cashier_order_history', compact('user', 'orderSummary', 'dateLabel', 'filter'));
    }

    public function searchOrderHistory(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.get');
        }

        // Get the search query
        $searchQuery = $request->input('searchQueryInput');
        $orders = Order::query()->where('branch_id', $user->branch_id); // Filter by branch_id

        // Check if searchQuery is not empty and filter accordingly
        if (!empty($searchQuery)) {
            // Try to parse the search query as a date
            try {
                $date = Carbon::createFromFormat('Y-m-d', $searchQuery);

                if ($date) {
                    // If it's a valid date
                    $orders->whereDate('created_at', $date);
                } else {
                    // If it can't be parsed as a date, try to interpret it as a year, month, or day of the week
                    if (preg_match('/^\d{4}$/', $searchQuery)) {
                        // If it's a year (YYYY)
                        $orders->whereYear('created_at', $searchQuery);
                    } elseif (preg_match('/^\d{1,2}$/', $searchQuery)) {
                        // If it's a month (MM)
                        $orders->whereMonth('created_at', $searchQuery);
                    } else {
                        // Check for days of the week (e.g., Monday, Tuesday)
                        $daysOfWeek = [
                            'sunday' => 0,
                            'monday' => 1,
                            'tuesday' => 2,
                            'wednesday' => 3,
                            'thursday' => 4,
                            'friday' => 5,
                            'saturday' => 6,
                        ];

                        $queryLower = strtolower($searchQuery);

                        if (array_key_exists($queryLower, $daysOfWeek)) {
                            // Get the current month and year
                            $currentMonth = now()->month;
                            $currentYear = now()->year;

                            // Filter orders by the specific day of the week
                            $orders->whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $currentMonth)
                                ->whereDay('created_at', $daysOfWeek[$queryLower]);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Handle the exception or log it
                return back()->withErrors(['searchQueryInput' => 'Invalid date format.']);
            }
        }

        // Get the filtered orders
        $orderSummary = $orders->get()->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d'); // Group orders by date
        })->map(function ($orders, $date) {
            $totalSales = $orders->sum(function ($order) {
                return $order->price * $order->quantity;
            });

            $totalProfit = $totalSales * 0.2; // Example: 20% profit margin

            return [
                'date' => $date,
                'total_sales' => $totalSales,
                'total_profit' => $totalProfit,
            ];
        });

        // Check if no orders found
        $noOrders = $orderSummary->isEmpty();

        // Return JSON response for AJAX
        if ($request->ajax()) {
            return response()->json([
                'html' => view('Partial.order_summary', compact('orderSummary'))->render(),
                'noOrders' => $noOrders,
            ]);
        }

        // For non-AJAX requests, return the view normally
        return view('Cashier.cashier_order_history', compact('user', 'orderSummary'));
    }

    public function viewReceipt(Request $request) {
        $user = Auth::user();
        $date = $request->query('date');
        Log::info('Received request to view receipt with date: ' . $date);

        // Check if the date is being received correctly
        if (!$date) {
            return abort(404, 'Date parameter missing');
        }

        // Fetch all order groups for that date with status "Served", filtered by branch
        $orderGroups = OrderGroup::where('status', 'Served')
            ->where('branch_id', $user->branch_id)
            ->whereHas('orders', function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->with('orders')
            ->get();

        if ($orderGroups->isNotEmpty()) {
            // Calculate the total sales for the day
            $totalSales = $orderGroups->sum(function ($group) {
                return $group->orders->sum(function ($order) {
                    return $order->price * $order->quantity;
                });
            });

            // Calculate the total profit for the day
            $totalProfit = $orderGroups->sum(function ($group) {
                return $group->orders->sum(function ($order) {
                    return $order->profit * $order->quantity;
                });
            });

            // Fetch the unique products and their total quantities
            $uniqueProducts = $orderGroups->flatMap(function ($group) {
                return $group->orders;
            })->groupBy('item') // Group by item_name
              ->map(function ($orders, $itemName) {
                  // Return the item, total quantity ordered, and other attributes
                  return [
                      'item_name' => $itemName,
                      'total_quantity' => $orders->sum('quantity'),
                      'image' => $orders->first()->image, // Assuming each item has the same image
                      'sauce' => $orders->first()->sauce // Assuming each item has the same sauce
                  ];
              });

            // Format the date
            $carbonDate = Carbon::parse($date);
            $formattedDate = $carbonDate->format('l, F j, Y'); // Format as 'Thursday, September 28, 2024'

            return view('Cashier.cashier_order_receipt', compact('user', 'orderGroups', 'formattedDate', 'totalSales', 'totalProfit', 'uniqueProducts'));
        } else {
            return redirect()->back()->with('error', 'Receipt not found for the selected date.');
        }
    }



    public function downloadReceipt($id) {
        // Fetch the order group based on the ID
        $orderGroup = OrderGroup::findOrFail($id);

        // Assuming e_receipt is the filename or path to the e-receipt stored
        $receiptPath = $orderGroup->e_receipt;

        // Check if the receipt file exists
        if (Storage::exists($receiptPath)) {
            // Extract the payment method and format the date
            $paymentMethod = ucfirst($orderGroup->payment_method); // Capitalize the payment method
            $date = $orderGroup->created_at->format('Y-m-d'); // Format date (assuming created_at is available)

            // Get the actual file extension
            $extension = pathinfo($receiptPath, PATHINFO_EXTENSION); // Extract the extension from the path

            // Create a custom filename
            $filename = "{$paymentMethod}_receipt_{$date}.{$extension}"; // Use the actual file extension

            // Return the file as a download response with the custom filename
            return Storage::download($receiptPath, $filename);
        } else {
            return redirect()->back()->with('error', 'Receipt not found.');
        }
    }
    public function viewCashierProfile(){
        $user = Auth::user();
        if ($user) {
            return view('Cashier.cashier_profile', compact('user'));
        }
        return redirect()->route('login.get');
    }









}

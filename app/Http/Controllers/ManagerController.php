<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ManagerController extends Controller
{
    public function viewManagerDashboard(){
        $user = Auth::user();
        if ($user) {
            $drinks = ['Mountain Dew', 'Royal', 'Coke', 'Water', 'Sprite'];

            // Fetch the top 3 trending food items with price
            $trendingFood = DB::table('orders')
                ->select('item', 'sauce', 'image', 'price', DB::raw('count(*) as total'))
                ->whereNotIn('item', $drinks)
                ->groupBy('item', 'sauce', 'image', 'price')
                ->orderBy('total', 'desc')
                ->limit(2)
                ->get();

            // Fetch the top 3 trending drinks with price
            $trendingDrink = DB::table('orders')
                ->select('item', 'image', 'price', DB::raw('count(*) as total'))
                ->whereIn('item', $drinks)
                ->groupBy('item', 'image', 'price')
                ->orderBy('total', 'desc')
                ->limit(3)
                ->get();

            // Get the current year
            $currentYear = date('Y');

            // Calculate total sales for the current year
            $totalSales = Order::whereYear('created_at', $currentYear)
                ->sum(DB::raw('quantity * price'));

            // Calculate total profit for the current year using the existing profit value in orders
            $totalProfit = Order::whereYear('created_at', $currentYear)
                ->sum(DB::raw('quantity * profit'));

            // Fetch sauce request counts
            $sauceCounts = DB::table('orders')
                ->select('sauce', DB::raw('count(*) as total'))
                ->whereYear('created_at', $currentYear)
                ->groupBy('sauce')
                ->orderBy('total', 'desc')
                ->get();

            // Prepare data for the pie chart
            $chartData = [
                'labels' => $sauceCounts->pluck('sauce')->toArray(),
                'data' => $sauceCounts->pluck('total')->toArray(),
            ];

            // Fetch weekly sales and profit data based on the current week
            $weeklySalesData = OrderGroup::where('status', 'Served')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->with('orders')
                ->get();

                $weeklySales = [];
                $weeklyProfit = [];

                // Initialize sales and profit for each day of the week
                $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($daysOfWeek as $day) {
                    $weeklySales[$day] = 0;
                    $weeklyProfit[$day] = 0;
                }

                foreach ($weeklySalesData as $group) {
                    $dayName = $group->created_at->format('l'); // Ensure this is valid
                    if (in_array($dayName, $daysOfWeek)) { // Check if it's a valid day
                        $dailySales = $group->orders->sum(fn($order) => $order->price * $order->quantity);
                        $dailyProfit = $group->orders->sum(fn($order) => $order->profit * $order->quantity);

                        // Add to the corresponding day
                        $weeklySales[$dayName] += $dailySales;
                        $weeklyProfit[$dayName] += $dailyProfit;
                    }
                }

                // Prepare data for the weekly sales graph
                $weeklyChartData = [
                    'labels' => $daysOfWeek, // Monday to Sunday
                    'sales' => array_values($weeklySales),
                    'profit' => array_values($weeklyProfit),
                ];



            return view('Manager.manager_dashboard', compact('user', 'totalSales', 'totalProfit', 'trendingFood', 'trendingDrink', 'chartData', 'weeklyChartData'));
        }

        return redirect()->route('login.get');
    }
    public function getWeeklyData(Request $request){
        $date = $request->input('date');
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $endOfWeek = Carbon::parse($date)->endOfWeek();

        // Fetch weekly sales and profit data
        $weeklySalesData = OrderGroup::where('status', 'Served')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->with('orders')
            ->get();

        $weeklySales = [];
        $weeklyProfit = [];

        foreach ($weeklySalesData as $group) {
            $dayName = $group->created_at->format('l'); // Get the day name (e.g., 'Monday')
            $dailySales = $group->orders->sum(fn($order) => $order->price * $order->quantity);
            $dailyProfit = $group->orders->sum(fn($order) => $order->profit * $order->quantity);

            // Initialize the arrays for each day of the week
            if (!isset($weeklySales[$dayName])) {
                $weeklySales[$dayName] = 0;
            }
            if (!isset($weeklyProfit[$dayName])) {
                $weeklyProfit[$dayName] = 0;
            }

            $weeklySales[$dayName] += $dailySales;
            $weeklyProfit[$dayName] += $dailyProfit;
        }

        // Ensure all days of the week are included
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($daysOfWeek as $day) {
            if (!isset($weeklySales[$day])) {
                $weeklySales[$day] = 0; // Set to 0 if no sales that day
            }
            if (!isset($weeklyProfit[$day])) {
                $weeklyProfit[$day] = 0; // Set to 0 if no profit that day
            }
        }

        return response()->json([
            'sales' => $weeklySales,
            'profit' => $weeklyProfit,
        ]);
    }
    public function viewManagerOrderHistory() {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.get');
        }

        // Retrieve all orders that belong to 'Served' order groups
        $orders = Order::whereHas('orderGroup', function ($query) {
            $query->where('status', 'Served');
        })->get();

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

        return view('Manager.manager_order_history', compact('user', 'orderSummary'));
    }
    public function filterManagerOrderHistory(Request $request) {
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

        switch ($filter) {
            case 'today':
                $orders = Order::whereDate('created_at', $today)->get();
                $dateLabel = $today->format('Y-m-d');
                break;
            case 'yesterday':
                $orders = Order::whereDate('created_at', $yesterday)->get();
                $dateLabel = $yesterday->format('Y-m-d');
                break;
            case 'this-month':
                $orders = Order::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->get();
                $dateLabel = now()->format('F Y'); // This month label (e.g., September 2024)
                break;
            case 'this-year':
                $orders = Order::whereBetween('created_at', [$thisYearStart, $thisYearEnd])->get();
                $dateLabel = now()->format('Y'); // This year label (e.g., 2024)
                break;
            default:
                // Handle case for 'all' or other defaults
                $orders = Order::all();
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
        return view('Manager.manager_order_history', compact('user', 'orderSummary', 'dateLabel', 'filter'));
    }
    public function searchOrderHistory(Request $request){
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.get');
        }

        // Get the search query
        $searchQuery = $request->input('searchQueryInput');
        $orders = Order::query();

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
        return view('Manager.manager_order_history', compact('user', 'orderSummary'));
    }
    public function viewReceipt(Request $request) {
        $user = Auth::user();
        $date = $request->query('date');
        Log::info('Received request to view receipt with date: ' . $date);

        // Check if the date is being received correctly
        if (!$date) {
            return abort(404, 'Date parameter missing');
        }

        // Fetch all order groups for that date with status "Served", along with their orders
        $orderGroups = OrderGroup::where('status', 'Served') // Add status condition
            ->whereHas('orders', function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->with('orders')
            ->get();

        if ($orderGroups->isNotEmpty()) {
            // Calculate the total sales for the day
            $totalSales = $orderGroups->sum(function ($group) {
                return $group->orders->sum(function ($order) {
                    return $order->price * $order->quantity; // Assuming 'price' and 'quantity' are the columns
                });
            });

            // Calculate the total profit for the day
            $totalProfit = $orderGroups->sum(function ($group) {
                return $group->orders->sum(function ($order) {
                    return $order->profit * $order->quantity; // Using actual profit values
                });
            });

            $carbonDate = Carbon::parse($date);
            $formattedDate = $carbonDate->format('l, F j, Y'); // Format as 'Thursday, September 28, 2024'

            return view('Manager.manager_order_receipt', compact('user', 'orderGroups', 'formattedDate', 'totalSales', 'totalProfit'));
        } else {
            return redirect()->back()->with('error', 'Receipt not found for the selected date.');
        }
    }
    public function viewCashierManagement() {
        if (Auth::check()) {
            $user = Auth::user();
            // Retrieve all users with 'cashier' role
            $cashierUsers = User::where('role', 'cashier')->get();
            $countCashierUsers = User::where('role', 'cashier')->count();

            // Return the view with the authenticated user and cashier data
            return view('Manager.manager_cashier_management', compact('user', 'cashierUsers', 'countCashierUsers'));
        }

        // Redirect to login page if user is not authenticated
        return redirect()->route('login.get');
    }
    public function deleteCashier($id) {
        $cashier = User::findOrFail($id);

        // Check if the user is a cashier before deleting
        if ($cashier->role !== 'cashier') {
            return response()->json(['status' => 'error', 'message' => 'User is not a cashier'], 403);
        }

        // Delete the cashier account
        $cashier->delete();

        return response()->json(['status' => 'success', 'message' => 'Cashier account deleted successfully!']);
    }
    public function createCashier(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'sex' => 'required|in:male,female',
        ]);

        // Create the new cashier user
        $cashier = User::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Hash the password
            'name' => $validatedData['name'],
            'contact_number' => $validatedData['contact_number'],
            'sex' => $validatedData['sex'],
            'role' => 'cashier', // Assign cashier role
        ]);

        return response()->json(['status' => 'success', 'message' => 'Cashier account created successfully!', 'cashier' => $cashier]);
    }
    public function viewBranchManagement() {
        if (Auth::check()) {
            $user = Auth::user();
            // Retrieve all users with 'cashier' role
            $branches = Branch::all();

            // Return the view with the authenticated user and cashier data
            return view('Manager.manager_branch_management', compact('user', 'branches'));
        }

        // Redirect to login page if user is not authenticated
        return redirect()->route('login.get');
    }
    public function createBranch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

       $branch =  Branch::create($request->all());
        return response()->json(['status' => 'success', 'message' => 'Branch created successfully!', 'branch' => $branch]);
    }
    public function deleteBranch($id) {
        $branch = Branch::findOrFail($id);

        // Check if the user is a branch before deleting
        if (!$branch) {
            return response()->json(['status' => 'error', 'message' => 'Branch do not exists'], 403);
        }

        // Delete the branch account
        $branch->delete();

        return response()->json(['status' => 'success', 'message' => 'Branch deleted successfully!']);
    }











}

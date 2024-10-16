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

class ManagerController extends Controller
{
    public function viewManagerDashboard() {
        $user = Auth::user();
        if ($user) {
            $drinks = ['Mountain Dew', 'Royal', 'Coke', 'Water (CvSU)', 'Water', 'Sprite'];

            // Fetch the top 2 trending food items with price
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

            // Calculate total profit for the current year
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
                $dayName = $group->created_at->format('l');
                if (in_array($dayName, $daysOfWeek)) {
                    $dailySales = $group->orders->sum(fn($order) => $order->price * $order->quantity);
                    $dailyProfit = $group->orders->sum(fn($order) => $order->profit * $order->quantity);

                    // Add to the corresponding day
                    $weeklySales[$dayName] += $dailySales;
                    $weeklyProfit[$dayName] += $dailyProfit;
                }
            }

            // Prepare data for the weekly sales graph
            $weeklyChartData = [
                'labels' => $daysOfWeek,
                'sales' => array_values($weeklySales),
                'profit' => array_values($weeklyProfit),
            ];

            // Check if trending food or drink are empty and set default values
            if ($trendingFood->isEmpty()) {
                $trendingFood = collect([['item' => 'No trending food', 'sauce' => '', 'image' => '', 'price' => 0]]);
            }

            if ($trendingDrink->isEmpty()) {
                $trendingDrink = collect([['item' => 'No trending drink', 'image' => '', 'price' => 0]]);
            }

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
    public function viewManagerOrderHistory(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.get');
        }

        // Get branch_id from the request
        $branchId = $request->query('branch_id');
        $branch = Branch::findOrFail(decrypt($branchId));
        // Retrieve all orders that belong to 'Served' order groups for the specified branch
        $orders = Order::whereHas('orderGroup', function ($query) {
            $query->where('status', 'Served');
        })->where('branch_id', $branch->id) // Filter by branch_id
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

        return view('Manager.manager_order_history', compact('user', 'orderSummary', 'branch'));
    }

    public function filterManagerOrderHistory(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.get');
        }

        // Retrieve branch_id from the request
        $branchId = $request->input('branch_id');
        $branch = Branch::findOrFail(decrypt($branchId));

        // Define date ranges for various filters
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();
        $thisYearStart = now()->startOfYear();
        $thisYearEnd = now()->endOfYear();

        // Determine the selected filter
        $filter = $request->input('filter', 'all');

        // Initialize the orders query
        $query = Order::where('branch_id', $branch->id); // Filter by branch_id

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
        return view('Manager.manager_order_history', compact('user', 'orderSummary', 'dateLabel', 'filter', 'branch'));
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
        $branchId = $request->query('branch_id'); // Retrieve branch ID from the query
        $branch = Branch::findOrFail($branchId);
        Log::info('Received request to view receipt with date: ' . $date . ' and branch ID: ' . $branchId);

        // Check if the date and branch ID are being received correctly
        if (!$date || !$branchId) {
            return abort(404, 'Date or branch parameter missing');
        }

        // Fetch all order groups for that date with status "Served", along with their orders for the specific branch
        $orderGroups = OrderGroup::where('status', 'Served') // Add status condition
            ->where('branch_id', $branchId) // Filter by branch ID
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


            $carbonDate = Carbon::parse($date);
            $formattedDate = $carbonDate->format('l, F j, Y'); // Format as 'Thursday, September 28, 2024'

            return view('Manager.manager_order_receipt', compact('user', 'orderGroups', 'formattedDate', 'totalSales', 'totalProfit', 'branch', 'uniqueProducts'));
        } else {
            return redirect()->back()->with('error', 'Receipt not found for the selected date and branch.');
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
    public function createCashier(Request $request) {
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
    public function viewCashierProfile($cashierId){
        // Check if the user is authenticated and retrieve the cashier
        if (auth()->check()) {
            $cashier = User::find(decrypt($cashierId));
            $user = Auth::user();
            // Check if the cashier exists
            if ($cashier) {
                return view('Manager.manager_view_cashier', compact('cashier', 'user'));
            }

            return redirect()->back()->with('error', "This cashier does not exist in the database");
        }

        return redirect()->route('login')->with('error', 'You need to be logged in to view this profile.');
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
    public function createBranch(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);
        // Handle the file upload
        if ($request->hasFile('image')) {
            // Store the file in 'public/inventory_image' and get the file name
            $fileName = $request->file('image')->store('branch_images', 'public');
        } else {
            return response()->json(['status' => 'error', 'message' => 'Image upload failed. Please try again.']);
        }
        // Create the inventory item with the uploaded image path
        $branch = Branch::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'image' => $fileName,
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Branch created successfully!',
            'branch' => $branch,
        ]);
    }
    public function deleteBranch($id) {
        $branch = Branch::findOrFail($id);

        // Check if the user is a branch before deleting
        if (!$branch) {
            return response()->json(['status' => 'error', 'message' => 'Branch do not exists'], 403);
        }

          // Check if the e-receipt file exists in storage
          if (Storage::exists($branch->image)) {
            // Delete the e-receipt file
            Storage::delete($branch->image);
        } else {
            // Optionally log or handle the case where the e-receipt does not exist
            Log::warning("Item Image does not exist: {$branch->image}");
        }

        // Delete the branch account
        $branch->delete();

        return response()->json(['status' => 'success', 'message' => 'Branch deleted successfully!']);
    }
    public function assignBranch(Request $request, $branchId) {
        $user = User::findOrFail(decrypt($request->cashier_id));
        $user->branch_id = decrypt($branchId);
        $user->save();
        return redirect()->back()->with('success', 'New cashier has been assigned successfully!');
    }
    public function viewBranch($branchId) {
        $user = Auth::user();
        $allCashiers = User::where('role', 'cashier')
                            ->where('branch_id', null)
                            ->get();
        $branch = Branch::findOrFail(decrypt($branchId));

        if (!$branch) {
            return response()->json(['status' => 'error', 'message' => 'Branch does not exist'], 403);
        }

        // Retrieve all cashiers associated with this branch
        $cashiers = User::where('branch_id', $branch->id)
                        ->where('role', 'cashier')
                        ->get();

        // Calculate total sales for the branch
        $totalSales = Order::where('branch_id', $branch->id)
                            ->sum(DB::raw('quantity * price'));

        // Calculate total profit for the branch
        $totalProfit = Order::where('branch_id', $branch->id)
                            ->sum(DB::raw('quantity * profit')); // Assuming you have a 'profit' column

        return view('Manager.manager_view_branch', compact('user', 'branch', 'cashiers', 'allCashiers', 'totalSales', 'totalProfit'));
    }

    public function removeAssignedCashier($cashierId){
        $cashier = User::findOrFail($cashierId);
        $cashier->branch_id = null;
        $cashier->save();
        return response()->json(['status' => 'success', 'message' => 'Cashier successfully removed to this branch!']);
    }
    public function viewInventoryManagement() {
        if (Auth::check()) {
            $user = Auth::user();
            // Retrieve all users with 'cashier' role
            $inventories = Inventory::all();

            // Return the view with the authenticated user and cashier data
            return view('Manager.manager_inventory_management', compact('user', 'inventories'));
        }

        // Redirect to login page if user is not authenticated
        return redirect()->route('login.get');
    }
    public function createInventory(Request $request) {
    // Validate request fields, including image file
    $request->validate([
        'item_name' => 'required|string|max:255',
        'item_quantity' => 'required|numeric|min:1',
        'item_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // Limit to 5MB and specific image types
    ]);

    // Handle the file upload
    if ($request->hasFile('item_image')) {
        // Store the file in 'public/inventory_image' and get the file name
        $fileName = $request->file('item_image')->store('inventory_images', 'public');
    } else {
        return response()->json(['status' => 'error', 'message' => 'Image upload failed. Please try again.']);
    }

    // Create the inventory item with the uploaded image path
    $inventory = Inventory::create([
        'item_name' => $request->input('item_name'),
        'item_quantity' => $request->input('item_quantity'),
        'item_image' => $fileName,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Inventory item created successfully!',
        'inventory' => $inventory,
    ]);
    }

    public function deleteInventory($id) {
        $inventory = Inventory::findOrFail($id);

        // Check if the user is a cashier before deleting
        if (!$inventory) {
            return response()->json(['status' => 'error', 'message' => 'Inventory item do not exist'], 404);
        }

        // Check if the e-receipt file exists in storage
        if (Storage::exists($inventory->item_image)) {
            // Delete the e-receipt file
            Storage::delete($inventory->item_image);
        } else {
            // Optionally log or handle the case where the e-receipt does not exist
            Log::warning("Item Image does not exist: {$inventory->item_image}");
        }

        // Delete the cashier account
        $inventory->delete();

        return response()->json(['status' => 'success', 'message' => 'Inventory item deleted successfully!']);
    }

    public function show($id) {
        $inventory = Inventory::findOrFail($id); // Find the inventory item by ID
        return response()->json(['status' => 'success', 'inventory' => $inventory]);
    }
    public function updateInventory(Request $request, $id) {

        // Validate the incoming request
        $request->validate([
            'item_name' => 'nullable|string|max:255',
            'item_quantity' => 'nullable|numeric|min:1',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Find the inventory item by ID
        $inventory = Inventory::findOrFail($id);

        // Update the inventory item's attributes
        if ($request->has('item_name')) {
            $inventory->item_name = $request->item_name;
        }
        if ($request->has('item_quantity')) {
            $inventory->item_quantity = $request->item_quantity;
        }

        // Check if a new image is uploaded
        if ($request->hasFile('item_image')) {
            // Delete the old image if it exists (optional)
            if ($inventory->item_image) {
                // Assuming the old image is stored in the 'public' disk
                Storage::disk('public')->delete($inventory->item_image);
            }

            // Store the new image
            $imagePath = $request->file('item_image')->store('inventory_images', 'public');
            // Update the image path in the inventory
            $inventory->item_image = $imagePath;
        }

        // Save the updated inventory item
        $inventory->save();

        // Redirect back with a success message
        return redirect()->back()
            ->with('success', 'Inventory item updated successfully!');
    }

















}

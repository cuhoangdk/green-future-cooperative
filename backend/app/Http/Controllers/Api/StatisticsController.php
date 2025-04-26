<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatisticsRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductCategory;

class StatisticsController extends Controller
{
    public function getStatistics(StatisticsRequest $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        // 1. Số khách hàng mới
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 2. Số thành viên mới
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // 3. Số mặt hàng mới và theo trạng thái
        $newProducts = Product::whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $productsByStatus = Product::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $productsByStatus = [
            'growing' => $productsByStatus['growing'] ?? 0,
            'selling' => $productsByStatus['selling'] ?? 0,
            'stopped' => $productsByStatus['stopped'] ?? 0,
        ];

        // 4. Số đơn hàng mới và theo trạng thái
        $newOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $ordersByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $ordersByStatus = [
            'pending' => $ordersByStatus['pending'] ?? 0,
            'processing' => $ordersByStatus['processing'] ?? 0,
            'delivering' => $ordersByStatus['delivering'] ?? 0,
            'delivered' => $ordersByStatus['delivered'] ?? 0,
            'cancelled' => $ordersByStatus['cancelled'] ?? 0,
        ];

        // 5. Số đơn hàng mỗi ngày
        $ordersPerDay = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // 6. Tổng doanh thu (tổng final_total_amount của đơn hàng delivered)
        $totalRevenue = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('final_total_amount');

        // 7. Top mặt hàng bán nhiều tiền nhất và nhiều số lượng nhất
        $topProductsByRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_units', 'products.unit_id', '=', 'product_units.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                'product_units.name as product_unit',
                DB::raw('SUM(order_items.total_item_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'product_units.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $topProductsByQuantity = Product::join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_units', 'products.unit_id', '=', 'product_units.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                'product_units.name as product_unit',
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.name', 'product_units.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // 8. Top thành viên bán nhiều tiền nhất
        $topUsersByRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('users', 'products.user_id', '=', 'users.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'users.id',
                'users.full_name',
                DB::raw('SUM(order_items.total_item_price) as total_revenue')
            )
            ->groupBy('users.id', 'users.full_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // 9. Top khách hàng mua nhiều tiền nhất
        $topCustomersByRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'customers.id',
                'customers.full_name',
                DB::raw('SUM(order_items.total_item_price) as total_revenue')
            )
            ->groupBy('customers.id', 'customers.full_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // 10. Số lượng và tỉ lệ sản phẩm theo product_categories
        $totalProducts = Product::count();
        $productsByCategory = Product::join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.id',
                'product_categories.name',
                DB::raw('COUNT(products.id) as product_count'),
                DB::raw('IF(? = 0, 0, ROUND((COUNT(products.id) / ? * 100), 2)) as percentage')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->setBindings([$totalProducts, $totalProducts])
            ->get();

        // 11. Số lượng và tỉ lệ sản phẩm được mua theo product_categories
        $totalPurchasedRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->sum('total_item_price');

        $purchasedByCategory = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw(
                '`product_categories`.`id`, `product_categories`.`name`, SUM(order_items.total_item_price) as total_revenue, IF(? = 0, 0, ROUND((SUM(order_items.total_item_price) / ? * 100), 2)) as percentage',
                [$totalPurchasedRevenue, $totalPurchasedRevenue]
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->get();

        // Response
        return response()->json([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'new_customers' => $newCustomers,
            'new_users' => $newUsers,
            'new_products' => $newProducts,
            'products_by_status' => $productsByStatus,
            'new_orders' => $newOrders,
            'orders_by_status' => $ordersByStatus,
            'orders_per_day' => $ordersPerDay,
            'total_revenue' => $totalRevenue,
            'top_products_by_revenue' => $topProductsByRevenue,
            'top_products_by_quantity' => $topProductsByQuantity,
            'top_users_by_revenue' => $topUsersByRevenue,
            'top_customers_by_revenue' => $topCustomersByRevenue,
            'products_by_category' => $productsByCategory,
            'purchased_by_category' => $purchasedByCategory,
        ]);
    }
}
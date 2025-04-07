<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Repositories\Eloquent\OrderRepository;
use App\Models\CartItem;
use App\Models\ProductQuantityPrice;
use App\Helpers\LocationHelper;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    // Hiển thị form checkout
    public function showCheckoutForm()
    {
        $customerId = Session::get('customer_id');    
        \Log::info('Customer ID in showCheckoutForm: ' . $customerId);

        // Lấy giỏ hàng từ cart_items
        $cartItems = DB::table('cart_items')
            ->where('customer_id', $customerId)
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select('cart_items.product_id', 'cart_items.quantity', 'products.name')
            ->get()
            ->map(function ($item) {
                try {
                    $price = $this->getPriceForQuantity($item->product_id, (float) $item->quantity);
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'name' => $item->name,
                        'price' => $price * $item->quantity,
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error getting price for product ID ' . $item->product_id . ': ' . $e->getMessage());
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'name' => $item->name,
                        'price' => 0,
                    ];
                }
            })->toArray();

        if (empty($cartItems)) {
            \Log::warning('Cart is empty for customer: ' . $customerId);
            $cartItems = [];
        }
        \Log::info('Cart Items in showCheckoutForm: ' . json_encode($cartItems));

        // Lấy danh sách địa chỉ của khách hàng
        $customerAddresses = CustomerAddress::where('customer_id', $customerId)
            ->with('address')
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'full_name' => $address->full_name,
                    'phone_number' => $address->phone_number,
                    'address_type' => $address->address_type,
                    'province' => $address->address->province,
                    'district' => $address->address->district,
                    'ward' => $address->address->ward,
                    'street_address' => $address->address->street_address,
                ];
            })->toArray();

        \Log::info('Customer Addresses: ' . json_encode($customerAddresses));
        $addressTypes = ['home', 'work', 'other'];
        // Lấy danh sách tỉnh từ API
        $provincesResponse = Http::get('https://esgoo.net/api-tinhthanh/1/0.htm');
        $provinces = $provincesResponse->successful() ? collect($provincesResponse->json()['data']) : collect();

        return view('vnpay.checkout', [
            'cartItems' => $cartItems,
            'provinces' => $provinces,
            'customerAddresses' => $customerAddresses,
            'addressTypes' => $addressTypes,
        ]);
    }

    // Xử lý tạo đơn hàng và chuyển hướng đến VNPAY
    public function create(Request $request)
    {
        $customerId = Session::get('customer_id');  
        \Log::info('Customer ID: ' . $customerId);

        $data = $request->only(['full_name', 'phone_number', 'province', 'district', 'ward', 'street_address', 'address_id', 'address_type']);
        \Log::info('Form Data: ' . json_encode($data));

        // Nếu chọn địa chỉ có sẵn
        if (!empty($data['address_id'])) {
            $selectedAddress = CustomerAddress::where('id', $data['address_id'])
                ->where('customer_id', $customerId)
                ->with('address')
                ->first();
            if ($selectedAddress) {
                $data['full_name'] = $selectedAddress->full_name;
                $data['phone_number'] = $selectedAddress->phone_number;
                $data['address_type'] = $selectedAddress->address_type;
                $data['province'] = $selectedAddress->address->province;
                $data['district'] = $selectedAddress->address->district;
                $data['ward'] = $selectedAddress->address->ward;
                $data['street_address'] = $selectedAddress->address->street_address;
            }
        }

        // Lấy giỏ hàng từ cart_items
        $cartItems = DB::table('cart_items')->where('customer_id', $customerId)->get()->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => (float) $item->quantity,
            ];
        })->toArray();
        if (empty($cartItems)) {
            \Log::warning('Cart is empty, using default items');
            $cartItems = [['product_id' => 1, 'quantity' => 2]];
        }
        \Log::info('Cart Items: ' . json_encode($cartItems));
        $data['items'] = $cartItems;

        session(['cart_backup' => $cartItems]);

        try {
            $data['created_by_method'] = 'customer';
            $result = $this->orderRepository->createForCustomer($customerId, $data);
            \Log::info('CreateForCustomer Result: ' . json_encode($result));

            if (empty($result['vnpay_url'])) {
                \Log::error('VNPAY URL is null or empty');
                throw new \Exception('Không thể tạo URL thanh toán VNPAY');
            }            
            return redirect($result['vnpay_url']);
        } catch (\Exception $e) {
            \Log::error('Error in createForCustomer: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xử lý kết quả trả về từ VNPAY
    public function vnpayReturn(Request $request)
    {
        \Log::info('VNPAY Return Request Received: ' . json_encode($request->all()));

        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);

        \Log::info('VNPAY Provided Hash: ' . $vnp_SecureHash);

        ksort($inputData);
        $hashData = http_build_query($inputData);
        $computedHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        \Log::info('Computed Hash Data: ' . $hashData);
        \Log::info('Computed Hash: ' . $computedHash);

        if ($computedHash === $vnp_SecureHash) {
            try {
                $order = $this->orderRepository->getById(null, $inputData['vnp_TxnRef']);
                \Log::info('Order Retrieved: ' . ($order ? $order->toJson() : 'null'));

                if (!$order) {
                    return view('vnpay.error', [
                        'message' => 'Không tìm thấy đơn hàng',
                        'code' => 'N/A',
                    ]);
                }

                if ($inputData['vnp_ResponseCode'] == '00') {
                    $order->update(['status' => 'paid']);
                    DB::table('cart_items')->where('customer_id', $order->customer_id)->delete();
                    session()->forget('cart_backup');
                    return view('vnpay.success', [
                        'order' => $order,
                        'transactionId' => $inputData['vnp_TransactionNo'],
                    ]);
                } else {
                    $cancelData = [
                        'cancelled_reason' => 'Thanh toán thất bại (Mã lỗi: ' . $inputData['vnp_ResponseCode'] . ')',
                    ];
                    $this->orderRepository->cancel(null, $inputData['vnp_TxnRef'], $cancelData);
                    \Log::info('Order Cancelled due to payment failure: ' . $order->toJson());

                    if ($order->created_by_method === 'customer' && $order->customer_id !== null) {
                        $cartBackup = session('cart_backup', []);
                        if (!empty($cartBackup)) {
                            foreach ($cartBackup as $item) {
                                DB::table('cart_items')->updateOrInsert(
                                    ['customer_id' => $order->customer_id, 'product_id' => $item['product_id']],
                                    ['quantity' => $item['quantity'], 'updated_at' => now()]
                                );
                            }
                            \Log::info('Cart Restored to cart_items: ' . json_encode($cartBackup));
                        } else {
                            \Log::warning('Cart backup is empty, nothing to restore');
                        }
                    }
                    session()->forget('cart_backup');

                    return view('vnpay.error', [
                        'message' => 'Thanh toán thất bại, đơn hàng đã bị hủy' . ($order->created_by_method === 'customer' && $order->customer_id ? ' và giỏ hàng đã được khôi phục' : ''),
                        'code' => $inputData['vnp_ResponseCode'],
                    ]);
                }
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                \Log::error('Order not found for TxnRef: ' . $inputData['vnp_TxnRef']);
                return view('vnpay.error', [
                    'message' => 'Không tìm thấy đơn hàng trong hệ thống',
                    'code' => 'N/A',
                ]);
            } catch (\Exception $e) {
                \Log::error('Error cancelling order: ' . $e->getMessage());
                return view('vnpay.error', [
                    'message' => 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage(),
                    'code' => $inputData['vnp_ResponseCode'] ?? 'N/A',
                ]);
            }
        } else {
            return view('vnpay.error', [
                'message' => 'Chữ ký không hợp lệ',
                'code' => 'N/A',
            ]);
        }
    }

    // Phương thức getPriceForQuantity
    protected function getPriceForQuantity(int $productId, float $quantity)
    {
        $minQuantity = ProductQuantityPrice::where('product_id', $productId)
            ->min('quantity');

        if ($minQuantity !== null && $quantity < $minQuantity) {
            throw new \Exception("Quantity {$quantity} for product ID {$productId} is below the minimum allowed quantity ({$minQuantity})");
        }

        $priceRecord = ProductQuantityPrice::where('product_id', $productId)
            ->where('quantity', '<=', $quantity)
            ->orderBy('quantity', 'desc')
            ->first();

        return $priceRecord ? $priceRecord->price : 0; // Giá mặc định nếu không tìm thấy
    }
}
<?php

namespace App\Http\Controllers;

use App\Repositories\Eloquent\OrderRepository;
use App\Models\CartItem;
use App\Models\ProductQuantityPrice;
use App\Helpers\LocationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $customerId = 5; // Giả định đã đăng nhập
        if (!$customerId) {
            return redirect()->route('login'); // Chuyển hướng nếu chưa đăng nhập
        }

        // Tạm thời không lấy từ giỏ hàng, để trống hoặc dùng dữ liệu mẫu
        $cartItems = [
            [
                'product_id' => 1,
                'quantity' => 2,
                'name' => 'Cà chua cherry đỏ chói',
                'price' => $this->getPriceForQuantity(1, 2),
            ],
            // Có thể thêm sản phẩm khác nếu cần
        ];

        // Lấy danh sách tỉnh/thành từ API
        $provincesResponse = Http::get('https://esgoo.net/api-tinhthanh/1/0.htm');
        $provinces = $provincesResponse->successful() ? collect($provincesResponse->json()['data']) : collect();

        return view('vnpay.checkout', [
            'cartItems' => $cartItems,
            'provinces' => $provinces,
        ]);
    }

    // Xử lý tạo đơn hàng và chuyển hướng đến VNPAY
    public function create(Request $request)
    {
        $customerId = 5;
        if (!$customerId) {
            return redirect()->route('login');
        }

        // Lấy dữ liệu từ form thay vì từ giỏ hàng
        $data = $request->only(['full_name', 'phone_number', 'province', 'district', 'ward', 'street_address']);
        
        // Chuyển đổi ID tỉnh, huyện, xã sang tên bằng LocationHelper
        $data['province'] = LocationHelper::getLocationName("https://esgoo.net/api-tinhthanh/1/{$data['province']}.htm");
        $data['district'] = LocationHelper::getLocationName("https://esgoo.net/api-tinhthanh/2/{$data['district']}.htm");
        $data['ward'] = LocationHelper::getLocationName("https://esgoo.net/api-tinhthanh/3/{$data['ward']}.htm");

        // Dữ liệu items tạm thời hard-coded hoặc từ input nếu có
        $data['items'] = [
            ['product_id' => 1, 'quantity' => 2], // Dữ liệu mẫu
        ];

        $result = $this->orderRepository->createForAdmin($customerId, $data);
        // \Log::info('VNPAY URL: ' . $result['vnpay_url']);
        // dd($result['vnpay_url']);
        return redirect($result['vnpay_url']);
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
            if ($inputData['vnp_ResponseCode'] == '00') {
                $order = $this->orderRepository->getById(5, $inputData['vnp_TxnRef']);
                \Log::info('Order Retrieved: ' . ($order ? $order->toJson() : 'null'));
                if (!$order) {
                    return view('vnpay.error', [
                        'message' => 'Không tìm thấy đơn hàng',
                        'code' => 'N/A',
                    ]);
                }
                $order->update(['status' => 'paid']);
                return view('vnpay.success', [
                    'order' => $order,
                    'transactionId' => $inputData['vnp_TransactionNo'],
                ]);
            } else {
                return view('vnpay.error', [
                    'message' => 'Thanh toán thất bại',
                    'code' => $inputData['vnp_ResponseCode'],
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
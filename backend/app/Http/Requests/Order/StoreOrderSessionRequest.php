<?php

namespace App\Http\Requests\Order;

use App\Helpers\LocationHelper;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Session;

class StoreOrderSessionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'customer_address_id' => 'nullable|exists:customer_addresses,id',
            'full_name' => 'required_without:customer_address_id|string|max:255',
            'phone_number' => 'required_without:customer_address_id|string|max:20',
            'address_type' => 'required|in:home,work,other', // Bắt buộc vì form checkout luôn có
            'province' => [
                'required_without:customer_address_id',
                'string',
                function ($attribute, $value, $fail) {
                    if (!LocationHelper::isValidProvince($value)) {
                        $fail('Mã tỉnh không hợp lệ.');
                    }
                },
            ],
            'district' => [
                'required_without:customer_address_id',
                'string',
                function ($attribute, $value, $fail) {
                    $province = $this->input('province');
                    if (!$province || !LocationHelper::isValidDistrict($province, $value)) {
                        $fail('Mã quận/huyện không hợp lệ.');
                    }
                },
            ],
            'ward' => [
                'required_without:customer_address_id',
                'string',
                function ($attribute, $value, $fail) {
                    $district = $this->input('district');
                    if (!$district || !LocationHelper::isValidWard($district, $value)) {
                        $fail('Mã phường/xã không hợp lệ.');
                    }
                },
            ],
            'street_address' => 'required_without:customer_address_id|string',
        ];

        // Nếu không đăng nhập (khách vãng lai)
        if (!Session::has('customer_id')) {
            $rules['items'] = 'required|array';
            $rules['items.*.product_id'] = 'required|exists:products,id';
            $rules['items.*.quantity'] = 'required|numeric|min:1';
            $rules['email'] = 'nullable|email';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $customerId = Session::get('customer_id');
            $items = [];

            // Nếu đăng nhập, lấy items từ cart_items thay vì request
            if ($customerId) {
                $cartItems = \DB::table('cart_items')
                    ->where('customer_id', $customerId)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => (float) $item->quantity,
                        ];
                    })->toArray();

                $items = $cartItems ?: $this->input('items', []);
            } else {
                $items = $this->input('items', []);
            }

            foreach ($items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? null;

                $product = Product::with('unit')->find($productId);

                if (!$product) {
                    $validator->errors()->add("items.{$index}.product_id", 'Sản phẩm không tồn tại.');
                    continue;
                }

                // Kiểm tra allow_decimal
                if ($product->unit && !$product->unit->allow_decimal && floor($quantity) != $quantity) {
                    $validator->errors()->add(
                        "items.{$index}.quantity",
                        "Số lượng cho sản phẩm ID {$productId} phải là số nguyên (ví dụ: 1, 2,...)."
                    );
                }

                // Kiểm tra stock_quantity
                if ($quantity > $product->stock_quantity) {
                    $validator->errors()->add(
                        "items.{$index}.quantity",
                        "Số lượng cho sản phẩm ID {$productId} không được vượt quá kho hiện có ({$product->stock_quantity})."
                    );
                }
            }
        });
    }

    public function messages()
    {
        return [
            'required_without' => 'Trường :attribute là bắt buộc khi không chọn địa chỉ có sẵn.',
            'address_type.required' => 'Loại địa chỉ là bắt buộc.',
            'items.required' => 'Danh sách sản phẩm là bắt buộc khi tạo đơn hàng.',
        ];
    }
}
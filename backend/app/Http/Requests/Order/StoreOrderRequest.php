<?php

namespace App\Http\Requests\Order;

use App\Helpers\LocationHelper;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'address_type' => 'sometimes|in:home,work,other',
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
                    $province = $this->input('address.province');
                    if (!$province || !LocationHelper::isValidDistrict($province, $value)) {
                        $fail('Mã quận/huyện không hợp lệ.');
                    }
                },
            ],
            'ward' => [
                'required_without:customer_address_id',
                'string',
                function ($attribute, $value, $fail) {
                    $district = $this->input('address.district');
                    if (!$district || !LocationHelper::isValidWard($district, $value)) {
                        $fail('Mã phường/xã không hợp lệ.');
                    }
                },
            ],
            'street_address' => 'required_without:customer_address_id|string',
            'shipping_fee' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string',
            'expected_delivery_date' => 'nullable|date|after:today',
            
        ];

        // Nếu là admin, yêu cầu customer_id
        if (auth('api_users')->check()) {
            $rules['customer_id'] = 'required|exists:customers,id';
            $rules['items'] = 'required|array';
            $rules['items.*.product_id'] = 'required|exists:products,id';
            $rules['items.*.quantity'] = 'required|numeric|min:0.1';
        }

        return $rules;
    }
    public function withValidator($validator)
    {
        if (auth('api_users')->check()) {
            $validator->after(function ($validator) {
                $items = $this->input('items', []);

                foreach ($items as $index => $item) {
                    $productId = $item['product_id'] ?? null;
                    $quantity = $item['quantity'] ?? null;

                    $product = Product::with('unit')->find($productId);

                    if (!$product) {
                        $validator->errors()->add("items.{$index}.product_id", 'The selected product does not exist.');
                        continue;
                    }

                    // Kiểm tra allow_decimal
                    if ($product->unit && !$product->unit->allow_decimal && floor($quantity) != $quantity) {
                        $validator->errors()->add(
                            "items.{$index}.quantity",
                            "The quantity for product ID {$productId} must be an integer (e.g., 1, 2, etc.)."
                        );
                    }

                    // Kiểm tra stock_quantity
                    if ($quantity > $product->stock_quantity) {
                        $validator->errors()->add(
                            "items.{$index}.quantity",
                            "The quantity for product ID {$productId} must not exceed the available stock ({$product->stock_quantity})."
                        );
                    }
                }
            });
        }
    }
    public function messages()
    {
        return [
            'required_without' => 'The :attribute field is required when customer_address_id is not provided.',
            'customer_id.required' => 'The customer_id field is required when creating an order as an admin.',
            'items.required' => 'The items field is required when creating an order as an admin.',
        ];
    }
}



<?php

namespace App\Http\Requests\Order;

use App\Helpers\LocationHelper;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Lấy ID đơn hàng từ route để kiểm tra trạng thái
        $orderId = $this->route('id');
        $order = Order::findOrFail($orderId);

        // Nếu trạng thái là pending, cho phép sửa tất cả các trường
        if ($order->status === 'pending') {
            return [
            'status' => 'sometimes|in:pending,processing,delivered',
            'full_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20',
            'address_type' => 'sometimes|in:home,work,other',
            'province' => [
                'sometimes', // Chỉ kiểm tra nếu province có trong request
                'string',
                function ($attribute, $value, $fail) {
                    // Kiểm tra required_without:customer_address_id
                    if (!request()->has('customer_address_id') && empty($value)) {
                        $fail('Mã tỉnh là bắt buộc khi không có customer_address_id.');
                    }
                    // Kiểm tra tính hợp lệ của province
                    if (!LocationHelper::isValidProvince($value)) {
                        $fail('Mã tỉnh không hợp lệ.');
                    }
                },
            ],
            'district' => [
                'sometimes', // Chỉ kiểm tra khi district có trong request
                'string',
                function ($attribute, $value, $fail) {
                    // Kiểm tra required_without:customer_address_id
                    if (!request()->has('customer_address_id') && empty($value)) {
                        $fail('Mã quận/huyện là bắt buộc khi không có customer_address_id.');
                    }
                    // Kiểm tra tính hợp lệ của district dựa trên province
                    $province = $this->input('province'); // Lấy province từ request
                    if (!$province || !LocationHelper::isValidDistrict($province, $value)) {
                        $fail('Mã quận/huyện không hợp lệ.');
                    }
                },
            ],
            'ward' => [
                'sometimes', // Chỉ kiểm tra khi ward có trong request
                'string',
                function ($attribute, $value, $fail) {
                    // Kiểm tra required_without:customer_address_id
                    if (!request()->has('customer_address_id') && empty($value)) {
                        $fail('Mã phường/xã là bắt buộc khi không có customer_address_id.');
                    }
                    // Kiểm tra tính hợp lệ của ward dựa trên district
                    $district = $this->input('district'); // Lấy district từ request
                    if (!$district || !LocationHelper::isValidWard($district, $value)) {
                        $fail('Mã phường/xã không hợp lệ.');
                    }
                },
            ],
            'street_address' => 'sometimes|string',            
            'admin_note' => 'nullable|string',
            'expected_delivery_date' => 'nullable|date|after:today',            
            ];
        }
        // Nếu trạng thái không phải pending, chỉ cho phép sửa status và admin_note
        return [
            'status' => 'sometimes|in:processing,delivered', // Loại bỏ pending khỏi danh sách
            'admin_note' => 'nullable|string',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $orderId = $this->route('id');
            $order = Order::findOrFail($orderId);

            // Kiểm tra nếu status được gửi trong request
            if ($this->has('status')) {
                $newStatus = $this->input('status');

                // Không cho phép chuyển về pending nếu hiện tại không phải pending
                if ($order->status !== 'pending' && $newStatus === 'pending') {
                    $validator->errors()->add(
                        'status',
                        'Cannot change status back to pending from ' . $order->status . '.'
                    );
                }
            }
        });
    }

    public function messages()
    {
        return [
            'status.in' => 'The selected status is invalid.',
        ];
    }
}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Trạng Thái Đơn Hàng #{{ $order->id }}</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333; background-color: #f9f9f9; line-height: 1.6; -webkit-font-smoothing: antialiased; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <div style="background-color: #2e7d32; padding: 20px; text-align: center; border-bottom: 1px solid #267029;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 0.5px;">Green Future Cooperative</h1>
        </div>

        <div style="padding: 30px 25px;">
            <h2 style="color: #2e7d32; margin-bottom: 20px; font-size: 20px; border-bottom: 1px solid #eaeaea; padding-bottom: 15px;">Cập Nhật Trạng Thái Đơn Hàng #{{ $order->id }}</h2>

            <?php
                $statusTranslations = [
                    'pending' => 'Đang chờ xử lý',
                    'processing' => 'Đang xử lý',
                    'delivered' => 'Đã giao hàng',
                    'cancelled' => 'Đã hủy'
                ];
                $translatedStatus = $statusTranslations[$order->status] ?? $order->status;
                use App\Helpers\LocationHelper;      
                
                // Lấy tên phường/xã
                $addressName = LocationHelper::getLocationName("https://esgoo.net/api-tinhthanh/5/{$order->ward}.htm");
            ?>

            @if ($recipientType === 'customer')
                <p style="font-size: 16px; margin-bottom: 10px;">Kính gửi Khách hàng,</p>
                <p style="font-size: 16px; margin-bottom: 25px; background-color: #f5f8f5; padding: 15px; border-radius: 6px; border-left: 4px solid #2e7d32;">
                    Đơn hàng của bạn đã được cập nhật sang trạng thái <span style="color: #2e7d32; font-weight: bold;">{{ $translatedStatus }}</span>.
                </p>
            @elseif ($recipientType === 'seller')
                <p style="font-size: 16px; margin-bottom: 10px;">Kính gửi Người bán,</p>
                <p style="font-size: 16px; margin-bottom: 25px; background-color: #f5f8f5; padding: 15px; border-radius: 6px; border-left: 4px solid #2e7d32;">
                    Một đơn hàng bạn đang xử lý đã được cập nhật sang trạng thái <span style="color: #2e7d32; font-weight: bold;">{{ $translatedStatus }}</span>.
                </p>
            @elseif ($recipientType === 'super_admin')
                <p style="font-size: 16px; margin-bottom: 10px;">Kính gửi Quản trị viên cấp cao,</p>
                <p style="font-size: 16px; margin-bottom: 25px; background-color: #f5f8f5; padding: 15px; border-radius: 6px; border-left: 4px solid #2e7d32;">
                    Một đơn hàng trong hệ thống đã được cập nhật sang trạng thái <span style="color: #2e7d32; font-weight: bold;">{{ $translatedStatus }}</span>.
                </p>
            @endif

            <h3 style="color: #2e7d32; font-size: 18px; margin: 25px 0 15px 0; border-bottom: 1px solid #eaeaea; padding-bottom: 8px;">Thông Tin Đơn Hàng</h3>
            <ul style="list-style: none; margin-bottom: 20px; padding: 0;">
                <li style="margin-bottom: 10px; font-size: 14px; display: flex; align-items: baseline;">
                    <strong style="width: 150px; display: inline-block; color: #555555;">Mã Hóa Đơn:</strong> 
                    <a href="{{ env('FRONTEND_URL') }}/orders/{{ $order->id }}" style="color: #2e7d32; text-decoration: none;">{{ $order->id }}</a>
                </li>
                <li style="margin-bottom: 10px; font-size: 14px; display: flex; align-items: baseline;">
                    <strong style="width: 150px; display: inline-block; color: #555555;">Ngày Đặt:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}
                </li>
                <li style="margin-bottom: 10px; font-size: 14px; display: flex; align-items: baseline;">
                    <strong style="width: 150px; display: inline-block; color: #555555;">Tên Khách Hàng:</strong> {{ $order->full_name }}
                </li>
            </ul>

            <h3 style="color: #2e7d32; font-size: 18px; margin: 25px 0 15px 0; border-bottom: 1px solid #eaeaea; padding-bottom: 8px;">Chi Tiết Đơn Hàng</h3>
            <table style="border-collapse: collapse; width: 100%; margin: 15px 0;">
                <thead>
                    <tr>
                        <th style="background-color: #f0f7f0; color: #2e7d32; font-weight: 600; text-align: left; padding: 12px; font-size: 14px; border: 1px solid #dde8dd;">Mã Sản Phẩm</th>
                        <th style="background-color: #f0f7f0; color: #2e7d32; font-weight: 600; text-align: left; padding: 12px; font-size: 14px; border: 1px solid #dde8dd;">Tên Sản Phẩm</th>
                        <th style="background-color: #f0f7f0; color: #2e7d32; font-weight: 600; text-align: left; padding: 12px; font-size: 14px; border: 1px solid #dde8dd;">Số Lượng</th>
                        <th style="background-color: #f0f7f0; color: #2e7d32; font-weight: 600; text-align: left; padding: 12px; font-size: 14px; border: 1px solid #dde8dd;">Giá</th>
                        <th style="background-color: #f0f7f0; color: #2e7d32; font-weight: 600; text-align: left; padding: 12px; font-size: 14px; border: 1px solid #dde8dd;">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr style="background-color: {{ $loop->even ? '#fafafa' : 'transparent' }};">
                            <td style="padding: 12px; text-align: left; font-size: 14px; border: 1px solid #eaeaea; color: #444444;">{{ $item->product_snapshot['id'] }}</td>
                            <td style="padding: 12px; text-align: left; font-size: 14px; border: 1px solid #eaeaea; color: #444444;">
                                <a href="{{ env('FRONTEND_URL') }}/products/{{ $item->product_snapshot['slug'] }}" style="color: #2e7d32; text-decoration: none;">{{ $item->product_snapshot['product_name'] }}</a>
                            </td>
                            <td style="padding: 12px; text-align: left; font-size: 14px; border: 1px solid #eaeaea; color: #444444;">{{ $item->quantity }}</td>
                            <td style="padding: 12px; text-align: left; font-size: 14px; border: 1px solid #eaeaea; color: #444444;">{{ number_format($item->product_snapshot['price'], 0) }} VND</td>
                            <td style="padding: 12px; text-align: left; font-size: 14px; border: 1px solid #eaeaea; color: #444444;">{{ number_format($item->total_item_price, 0) }} VND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h3 style="color: #2e7d32; font-size: 18px; margin: 25px 0 15px 0; border-bottom: 1px solid #eaeaea; padding-bottom: 8px;">Tổng Giá</h3>
            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <ul style="list-style: none; margin-bottom: 20px; padding: 0;">
                    <li style="margin-bottom: 10px; font-size: 14px; display: flex; align-items: baseline;">
                        <strong style="width: 150px; display: inline-block; color: #555555;">Tổng Giá Sản Phẩm:</strong> {{ number_format($order->total_price, 0) }} VND
                    </li>
                    <li style="margin-bottom: 10px; font-size: 14px; display: flex; align-items: baseline;">
                        <strong style="width: 150px; display: inline-block; color: #555555;">Phí Vận Chuyển:</strong> {{ number_format($order->shipping_fee, 0) }} VND
                    </li>
                    <li style="margin-top: 15px; font-weight: bold; color: #2e7d32; font-size: 16px; border-top: 1px dashed #ddd; padding-top: 10px; display: flex; align-items: baseline;">
                        <strong style="width: 150px; display: inline-block; color: #555555;">Tổng Thanh Toán:</strong> {{ number_format($order->final_total_amount, 0) }} VND
                    </li>
                </ul>
            </div>

            <h3 style="color: #2e7d32; font-size: 18px; margin: 25px 0 15px 0; border-bottom: 1px solid #eaeaea; padding-bottom: 8px;">Địa Chỉ Giao Hàng</h3>
            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; color: #555555;">
                {{ $order->street_address }}, {{ $addressName }}
            </div>

            @if ($order->status === 'cancelled' && $order->cancelled_reason)
                <h3 style="color: #2e7d32; font-size: 18px; margin: 25px 0 15px 0; border-bottom: 1px solid #eaeaea; padding-bottom: 8px;">Lý Do Hủy</h3>
                <div style="background-color: #fff8f8; padding: 15px; border-radius: 6px; border-left: 4px solid #e57373; margin: 20px 0;">
                    {{ $order->cancelled_reason }}
                </div>
            @endif
        </div>

        <div style="background-color: #f5f5f5; padding: 20px; text-align: center; color: #666666; font-size: 14px; border-top: 1px solid #eaeaea;">
            <p style="margin: 0;">Trân trọng,</p>
            <span style="font-weight: bold; color: #2e7d32; margin: 5px 0 15px 0; display: block;">Green Future Cooperative</span>
            <div style="font-size: 12px; color: #888888; margin-top: 15px;">
                <p style="margin: 0;">© 2025 Green Future Cooperative. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </div>
</body>
</html>
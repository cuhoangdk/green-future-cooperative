<!DOCTYPE html>
<html>
<head>
    <title>Cập Nhật Trạng Thái Đơn Hàng</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Cập Nhật Trạng Thái Đơn Hàng #{{ $order->order_code }}</h1>
    
    <?php
        $statusTranslations = [
            'pending' => 'Đang chờ xử lý',
            'processing' => 'Đang xử lý',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy'
        ];
        $translatedStatus = $statusTranslations[$order->status] ?? $order->status;
    ?>

    @if ($recipientType === 'customer')
        <p>Kính gửi Khách hàng,</p>
        <p>Đơn hàng của bạn đã được cập nhật sang trạng thái <strong>{{ $translatedStatus }}</strong>.</p>
    @elseif ($recipientType === 'seller')
        <p>Kính gửi Người bán,</p>
        <p>Một đơn hàng bạn đang xử lý đã được cập nhật sang trạng thái <strong>{{ $translatedStatus }}</strong>.</p>
    @elseif ($recipientType === 'super_admin')
        <p>Kính gửi Quản trị viên cấp cao,</p>
        <p>Một đơn hàng trong hệ thống đã được cập nhật sang trạng thái <strong>{{ $translatedStatus }}</strong>.</p>
    @endif

    <h2>Thông Tin Đơn Hàng</h2>
    <ul>
        <li><strong>Mã Hóa Đơn:</strong> <a href="{{ config('app.url') }}/orders/{{ $order->id }}">{{ $order->order_code }}</a></li>
        <li><strong>Ngày Đặt:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</li>
        <li><strong>Tên Khách Hàng:</strong> {{ $order->full_name }}</li>
    </ul>

    <h2>Chi Tiết Đơn Hàng</h2>
    <table>
        <thead>
            <tr>
                <th>Mã Sản Phẩm</th>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Giá</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_id }}</td>
                    <td><a href="{{ config('app.url') }}/products/{{ $item->product_id }}">{{ $item->product_snapshot['name'] }}</a></td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 0) }} VND</td>
                    <td>{{ number_format($item->total_item_price, 0) }} VND</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Tổng Giá</h2>
    <ul>
        <li><strong>Tổng Giá Sản Phẩm:</strong> {{ number_format($order->total_price, 0) }} VND</li>
        <li><strong>Phí Vận Chuyển:</strong> {{ number_format($order->shipping_fee, 0) }} VND</li>
        <li><strong>Tổng Thanh Toán:</strong> {{ number_format($order->final_total_amount, 0) }} VND</li>
    </ul>

    <h2>Địa Chỉ Giao Hàng</h2>
    <p>{{ $order->street_address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}</p>

    @if ($order->status === 'cancelled' && $order->cancelled_reason)
        <h2>Lý Do Hủy</h2>
        <p>{{ $order->cancelled_reason }}</p>
    @endif

    <p>Xin cảm ơn,</p>
    <p>Đội ngũ của bạn</p>
</body>
</html>
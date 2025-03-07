<!DOCTYPE html>
<html>
<head>
    <title>Cập Nhật Trạng Thái Đơn Hàng</title>
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

    <h2>Chi Tiết Đơn Hàng</h2>
    <ul>
        <li><strong>Mã Đơn Hàng:</strong> {{ $order->order_code }}</li>
        <li><strong>Khách Hàng:</strong> {{ $order->full_name }}</li>
        <li><strong>Tổng Tiền:</strong> {{ number_format($order->final_total_amount, 0) }} VND</li>
        <li><strong>Trạng Thái:</strong> {{ $translatedStatus }}</li>
        @if ($order->status === 'cancelled' && $order->cancelled_reason)
            <li><strong>Lý Do Hủy:</strong> {{ $order->cancelled_reason }}</li>
        @endif
    </ul>

    <p>Xin cảm ơn,</p>
    <p>Đội ngũ của bạn</p>
</body>
</html>
<!-- resources/views/vnpay/success.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Thêm vào đầu form -->
    <div class="text-end mb-3">
        <a href="{{ route('logout') }}" class="btn btn-secondary">Đăng xuất</a>
    </div>
    <div class="container mt-5">
        <h1 class="text-center text-success">Thanh toán thành công!</h1>
        <div class="card mt-4">
            <div class="card-body">
                <p><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
                <p><strong>Tổng tiền:</strong> {{ number_format($order->final_total_amount) }} VND</p>
                <p><strong>Mã giao dịch VNPAY:</strong> {{ $transactionId }}</p>
                <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
            </div>
        </div>
        <a href="/" class="btn btn-primary mt-3">Quay lại trang chủ</a>
    </div>
</body>
</html>
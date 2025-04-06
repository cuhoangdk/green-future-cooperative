<!-- resources/views/vnpay/error.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-danger">Lỗi thanh toán</h1>
        <div class="card mt-4">
            <div class="card-body">
                <p><strong>Thông báo:</strong> {{ $message }}</p>
                <p><strong>Mã lỗi:</strong> {{ $code }}</p>
            </div>
        </div>
        <a href="/vnpay/checkout" class="btn btn-primary mt-3">Thử lại</a>
    </div>
</body>
</html>
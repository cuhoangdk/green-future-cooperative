<!-- resources/views/vnpay/checkout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VNPAY Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Checkout</h1>
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form action="{{ route('vnpay.checkout') }}" method="POST" class="w-75 mx-auto">
            @csrf
            <!-- Thông tin giỏ hàng -->
            <div class="mb-4">
                <h3>Giỏ hàng</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ number_format($item['price']) }} VND</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Thông tin người nhận -->
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="Nguyen Van A" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control" value="0901234567" required>
            </div>

            <!-- Dropdown địa chỉ -->
            <div class="mb-3">
                <label for="province" class="form-label">Province</label>
                <select name="province" id="province" class="form-select" required>
                    <option value="">Select Province</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province['id'] }}">{{ $province['full_name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="district" class="form-label">District</label>
                <select name="district" id="district" class="form-select" required>
                    <option value="">Select District</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="ward" class="form-label">Ward</label>
                <select name="ward" id="ward" class="form-select" required>
                    <option value="">Select Ward</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="street_address" class="form-label">Street Address</label>
                <input type="text" name="street_address" id="street_address" class="form-control" value="123 Đường Láng" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Pay with VNPAY</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('province').addEventListener('change', function() {
            const provinceId = this.value;
            const districtSelect = document.getElementById('district');
            districtSelect.innerHTML = '<option value="">Select District</option>';

            if (provinceId) {
                fetch(`https://esgoo.net/api-tinhthanh/2/${provinceId}.htm`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error === 0) {
                            data.data.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.id;
                                option.textContent = district.full_name;
                                districtSelect.appendChild(option);
                            });
                        }
                    });
            }
        });

        document.getElementById('district').addEventListener('change', function() {
            const districtId = this.value;
            const wardSelect = document.getElementById('ward');
            wardSelect.innerHTML = '<option value="">Select Ward</option>';

            if (districtId) {
                fetch(`https://esgoo.net/api-tinhthanh/3/${districtId}.htm`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error === 0) {
                            data.data.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.id;
                                option.textContent = ward.full_name;
                                wardSelect.appendChild(option);
                            });
                        }
                    });
            }
        });
    </script>
</body>
</html>
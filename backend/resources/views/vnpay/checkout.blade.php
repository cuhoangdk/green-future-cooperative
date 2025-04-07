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
    <!-- Thêm vào đầu form -->
    <div class="text-end mb-3">
        <a href="{{ route('logout') }}" class="btn btn-secondary">Đăng xuất</a>
    </div>
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
                @if (empty($cartItems))
                    <p>Giỏ hàng trống.</p>
                @else
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
                @endif
            </div>

            <!-- Chọn địa chỉ -->
            <div class="mb-3">
                <label for="address_select" class="form-label">Chọn địa chỉ</label>
                <select name="address_id" id="address_select" class="form-select">
                    <option value="">Tự nhập địa chỉ</option>
                    @foreach ($customerAddresses as $address)
                        <option value="{{ $address['id'] }}" 
                                data-fullname="{{ $address['full_name'] }}"
                                data-phone="{{ $address['phone_number'] }}"
                                data-province="{{ $address['province'] }}"
                                data-district="{{ $address['district'] }}"
                                data-ward="{{ $address['ward'] }}"
                                data-street="{{ $address['street_address'] }}"
                                data-type="{{ $address['address_type'] }}">
                            {{ $address['full_name'] }} - {{ $address['street_address'] }} ({{ $address['address_type'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Thông tin người nhận -->
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address_type" class="form-label">Address Type</label>
                <select name="address_type" id="address_type" class="form-select" required>
                    @foreach ($addressTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
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
                <input type="text" name="street_address" id="street_address" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100" {{ empty($cartItems) ? 'disabled' : '' }}>Pay with VNPAY</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('address_select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                document.getElementById('full_name').value = selectedOption.getAttribute('data-fullname');
                document.getElementById('phone_number').value = selectedOption.getAttribute('data-phone');
                document.getElementById('address_type').value = selectedOption.getAttribute('data-type');
                document.getElementById('province').value = selectedOption.getAttribute('data-province');
                document.getElementById('street_address').value = selectedOption.getAttribute('data-street');

                const provinceEvent = new Event('change');
                document.getElementById('province').dispatchEvent(provinceEvent);

                setTimeout(() => {
                    document.getElementById('district').value = selectedOption.getAttribute('data-district');
                    const districtEvent = new Event('change');
                    document.getElementById('district').dispatchEvent(districtEvent);

                    setTimeout(() => {
                        document.getElementById('ward').value = selectedOption.getAttribute('data-ward');
                    }, 500);
                }, 500);
            } else {
                document.getElementById('full_name').value = '';
                document.getElementById('phone_number').value = '';
                document.getElementById('address_type').value = 'home'; // Mặc định là home
                document.getElementById('province').value = '';
                document.getElementById('district').value = '';
                document.getElementById('ward').value = '';
                document.getElementById('street_address').value = '';
            }
        });

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
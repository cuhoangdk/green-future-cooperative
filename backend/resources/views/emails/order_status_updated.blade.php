<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Trạng Thái Đơn Hàng #{{ $order->id }}</title>
    <style>
        /* Reset CSS */
        body, h1, h2, h3, p, table, ul, li {
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333333;
            background-color: #f9f9f9;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Container */
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Header */
        .header {
            background-color: #2e7d32;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #267029;
        }
        
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.5px;
        }
        
        /* Content */
        .content {
            padding: 30px 25px;
        }
        
        .order-title {
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 15px;
        }
        
        .greeting {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .status-update {
            font-size: 16px;
            margin-bottom: 25px;
            background-color: #f5f8f5;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #2e7d32;
        }
        
        .status-highlight {
            color: #2e7d32;
            font-weight: bold;
        }
        
        .section-title {
            color: #2e7d32;
            font-size: 18px;
            margin: 25px 0 15px 0;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 8px;
        }
        
        /* Order Details */
        .info-list {
            list-style: none;
            margin-bottom: 20px;
        }
        
        .info-list li {
            margin-bottom: 10px;
            font-size: 14px;
            display: flex;
            align-items: baseline;
        }
        
        .info-list li strong {
            width: 150px;
            display: inline-block;
            color: #555555;
        }
        
        /* Table Styles */
        .order-table {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }
        
        .order-table th {
            background-color: #f0f7f0;
            color: #2e7d32;
            font-weight: 600;
            text-align: left;
            padding: 12px;
            font-size: 14px;
            border: 1px solid #dde8dd;
        }
        
        .order-table td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
            border: 1px solid #eaeaea;
            color: #444444;
        }
        
        .order-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .order-table a {
            color: #2e7d32;
            text-decoration: none;
        }
        
        .order-table a:hover {
            text-decoration: underline;
        }
        
        /* Price Summary */
        .price-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        
        .price-summary .info-list li:last-child {
            margin-top: 15px;
            font-weight: bold;
            color: #2e7d32;
            font-size: 16px;
            border-top: 1px dashed #ddd;
            padding-top: 10px;
        }
        
        /* Address */
        .address-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555555;
        }
        
        /* Cancel Reason */
        .cancel-reason {
            background-color: #fff8f8;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #e57373;
            margin: 20px 0;
        }
        
        /* Footer */
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            color: #666666;
            font-size: 14px;
            border-top: 1px solid #eaeaea;
        }
        
        .company-name {
            font-weight: bold;
            color: #2e7d32;
            margin: 5px 0 15px 0;
            display: block;
        }
        
        .copyright {
            font-size: 12px;
            color: #888888;
            margin-top: 15px;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                width: auto;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .order-title {
                font-size: 18px;
            }
            
            .order-table th, 
            .order-table td {
                font-size: 12px;
                padding: 8px;
            }
            
            .info-list li strong {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Green Future Cooperative</h1>
        </div>

        <div class="content">
            <h2 class="order-title">Cập Nhật Trạng Thái Đơn Hàng #{{ $order->id }}</h2>

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
                <p class="greeting">Kính gửi Khách hàng,</p>
                <p class="status-update">Đơn hàng của bạn đã được cập nhật sang trạng thái <span class="status-highlight">{{ $translatedStatus }}</span>.</p>
            @elseif ($recipientType === 'seller')
                <p class="greeting">Kính gửi Người bán,</p>
                <p class="status-update">Một đơn hàng bạn đang xử lý đã được cập nhật sang trạng thái <span class="status-highlight">{{ $translatedStatus }}</span>.</p>
            @elseif ($recipientType === 'super_admin')
                <p class="greeting">Kính gửi Quản trị viên cấp cao,</p>
                <p class="status-update">Một đơn hàng trong hệ thống đã được cập nhật sang trạng thái <span class="status-highlight">{{ $translatedStatus }}</span>.</p>
            @endif

            <h3 class="section-title">Thông Tin Đơn Hàng</h3>
            <ul class="info-list">
                <li>
                    <strong>Mã Hóa Đơn:</strong> 
                    <a href="{{ env('FRONTEND_URL') }}/orders/{{ $order->id }}" style="color: #2e7d32;">{{ $order->id }}</a>
                </li>
                <li><strong>Ngày Đặt:</strong> {{ $order->created_at->format('d/m/Y H:i:s') }}</li>
                <li><strong>Tên Khách Hàng:</strong> {{ $order->full_name }}</li>
            </ul>

            <h3 class="section-title">Chi Tiết Đơn Hàng</h3>
            <table class="order-table">
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
                            <td>{{ $item->product_snapshot['id'] }}</td>
                            <td><a href="{{ env('FRONTEND_URL') }}/products/{{ $item->product_snapshot['slug'] }}">{{ $item->product_snapshot['product_name'] }}</a></td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->product_snapshot['price'], 0) }} VND</td>
                            <td>{{ number_format($item->total_item_price, 0) }} VND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h3 class="section-title">Tổng Giá</h3>
            <div class="price-summary">
                <ul class="info-list">
                    <li><strong>Tổng Giá Sản Phẩm:</strong> {{ number_format($order->total_price, 0) }} VND</li>
                    <li><strong>Phí Vận Chuyển:</strong> {{ number_format($order->shipping_fee, 0) }} VND</li>
                    <li><strong>Tổng Thanh Toán:</strong> {{ number_format($order->final_total_amount, 0) }} VND</li>
                </ul>
            </div>

            <h3 class="section-title">Địa Chỉ Giao Hàng</h3>
            <div class="address-box">
                {{ $order->street_address }}, {{ $addressName }}
            </div>

            @if ($order->status === 'cancelled' && $order->cancelled_reason)
                <h3 class="section-title">Lý Do Hủy</h3>
                <div class="cancel-reason">
                    {{ $order->cancelled_reason }}
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Trân trọng,</p>
            <span class="company-name">Green Future Cooperative</span>
            <div class="copyright">
                <p>© 2025 Green Future Cooperative. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </div>
</body>
</html>
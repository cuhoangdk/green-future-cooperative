<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Minh Tài Khoản Của Bạn</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333; background-color: #f9f9f9; line-height: 1.6;">
    <div
        style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
        <div style="text-align: center; padding: 20px 0; border-bottom: 2px solid #eaeaea;">
            <h1 style="color: #2e7d32; margin: 0; font-size: 24px;">Green Future Cooperative</h1>
        </div>

        <div style="padding: 30px 20px; text-align: center;">
            <h2 style="color: #2e7d32; margin-top: 0; font-size: 22px;">Xin Chào!</h2>

            <p style="font-size: 16px; margin-bottom: 25px;">Cảm ơn bạn đã đăng ký tham gia cùng chúng tôi. Để hoàn tất
                quá trình đăng ký, vui lòng xác minh địa chỉ email của bạn.</p>

            <div style="margin: 30px 0;">
                <a href="{{ $url }}"
                    style="background-color: #2e7d32; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; font-size: 16px; transition: background-color 0.3s;">
                    Xác Minh Email
                </a>
            </div>

            <!-- Phần hiển thị link nếu nút không hoạt động -->
            <p style="font-size: 14px; color: #666666; margin-top: 20px;">Nếu nút trên không hoạt động, vui lòng sao
                chép và dán đường dẫn sau vào trình duyệt của bạn:</p>
            <p style="font-size: 14px; color: #2e7d32; word-break: break-all; margin-top: 10px;">{{ $url }}</p>

            <p style="font-size: 14px; color: #666666; margin-top: 30px;">Nếu bạn không tạo tài khoản, bạn không cần
                thực hiện thêm hành động nào.</p>
        </div>

        <div
            style="background-color: #f5f5f5; padding: 20px; border-radius: 0 0 8px 8px; text-align: center; color: #666666; font-size: 14px; border-top: 1px solid #eaeaea;">
            <p style="margin: 0;">Trân trọng,<br>
                <span style="font-weight: bold; color: #2e7d32;">Green Future Cooperative</span>
            </p>

            <div style="margin-top: 20px; font-size: 12px; color: #888888;">
                <p>© 2025 Green Future Cooperative. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </div>
</body>

</html>
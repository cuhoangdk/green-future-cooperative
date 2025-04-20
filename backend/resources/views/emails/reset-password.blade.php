<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Đặt Lại Mật Khẩu</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333; background-color: #f9f9f9; line-height: 1.6; -webkit-font-smoothing: antialiased; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <div style="background-color: #2e7d32; padding: 20px; text-align: center; border-bottom: 1px solid #267029;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 0.5px;">Green Future Cooperative</h1>
        </div>

        <div style="padding: 30px 25px; text-align: center;">
            <h2 style="color: #2e7d32; margin: 0 0 20px 0; font-size: 20px; border-bottom: 1px solid #eaeaea; padding-bottom: 15px;">Thông Báo Đặt Lại Mật Khẩu</h2>
            <p style="font-size: 16px; margin: 0 0 25px 0;">Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>

            <div>
                <a href="{{ $url }}" style="background-color: #2e7d32; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; font-size: 16px;">Đặt Lại Mật Khẩu</a>
            </div>

            <p style="font-size: 14px; color: #666666; margin: 20px 0 0 0;">
                Nếu nút trên không hoạt động, vui lòng sao chép và dán đường dẫn sau vào trình duyệt của bạn:<br>
                <a href="{{ $url }}" style="color: #2e7d32; word-break: break-all; text-decoration: none;">{{ $url }}</a>
            </p>

            <p style="font-size: 14px; color: #666666; margin: 30px 0 0 0;">Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.</p>
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
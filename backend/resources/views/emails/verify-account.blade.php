<!-- resources/views/emails/verify-account.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Xác Minh Tài Khoản Của Bạn</title>
</head>
<body>
    <h1>Xin Chào!</h1>
    
    <p>Cảm ơn bạn đã đăng ký. Vui lòng xác minh địa chỉ email của bạn.</p>
    
    <p>
        <a href="{{ $url }}" 
           style="background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">
            Xác Minh Email
        </a>
    </p>
    
    <p>Nếu bạn không tạo tài khoản, bạn không cần thực hiện thêm hành động nào.</p>
    
    <p>Trân trọng,<br>
    Green Future Cooperative</p>
</body>
</html>
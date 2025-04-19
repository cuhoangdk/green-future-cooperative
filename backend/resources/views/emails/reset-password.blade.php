<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Đặt Lại Mật Khẩu</title>
    <style>
        /* Reset CSS */
        body, h1, h2, p {
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
            text-align: center;
        }

        .content h2 {
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 15px;
        }

        .content p {
            font-size: 16px;
            margin-bottom: 25px;
        }

        /* Button */
        .reset-button {
            background-color: #2e7d32;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .reset-button:hover {
            background-color: #267029;
            text-decoration: none;
        }

        /* Fallback Link */
        .fallback-link {
            font-size: 14px;
            color: #666666;
            margin-top: 20px;
        }

        .fallback-link a {
            color: #2e7d32;
            word-break: break-all;
            text-decoration: none;
        }

        .fallback-link a:hover {
            text-decoration: underline;
        }

        /* Note */
        .note {
            font-size: 14px;
            color: #666666;
            margin-top: 30px;
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

            .content h2 {
                font-size: 18px;
            }

            .reset-button {
                padding: 10px 20px;
                font-size: 14px;
            }

            .fallback-link,
            .note {
                font-size: 12px;
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
            <h2>Thông Báo Đặt Lại Mật Khẩu</h2>
            <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>

            <div>
                <a href="{{ $url }}" class="reset-button">Đặt Lại Mật Khẩu</a>
            </div>

            <p class="fallback-link">Nếu nút trên không hoạt động, vui lòng sao chép và dán đường dẫn sau vào trình duyệt của bạn:<br>
                <a href="{{ $url }}">{{ $url }}</a>
            </p>

            <p class="note">Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.</p>
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
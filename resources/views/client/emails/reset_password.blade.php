<!DOCTYPE html>
<html>
<head>
    <title>Đặt lại mật khẩu</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: #111; text-transform: uppercase;">YÊU CẦU ĐẶT LẠI MẬT KHẨU</h2>
    </div>
    
    <p>Chào bạn,</p>
    <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại <strong>PBall Store</strong>.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}" style="background-color: #dc3545; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 3px; font-weight: bold; display: inline-block;">
            ĐẶT LẠI MẬT KHẨU MỚI
        </a>
    </div>
    
    <p>Liên kết đặt lại mật khẩu này sẽ hết hạn sau 60 phút.</p>
    <p>Nếu bạn không gửi yêu cầu này, xin vui lòng bỏ qua email và tài khoản của bạn vẫn an toàn.</p>
    
    <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">
    <p style="font-size: 12px; color: #888; text-align: center;">
        &copy; {{ date('Y') }} PBall Store. All rights reserved.
    </p>
</body>
</html>
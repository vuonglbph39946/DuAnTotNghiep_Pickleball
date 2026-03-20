<div style="background-color: #f4f6f9; padding: 40px 10px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eaeaea;">

        <div style="background-color: #dc3545; padding: 30px; text-align: center;">
            <h2 style="color: #ffffff; margin: 0; font-size: 26px; letter-spacing: 1px; text-transform: uppercase;">PBALL STORE</h2>
            <p style="color: #f8d7da; margin: 8px 0 0 0; font-size: 15px; font-weight: 500;">Thông Báo Yêu Cầu Hoàn Tiền</p>
        </div>

        <div style="padding: 30px 40px;">
            <p style="font-size: 16px; margin-top: 0;">Kính gửi quý khách <strong>{{ $order->customer_name }}</strong>,</p>

            <p style="font-size: 15px; color: #444;">Chúng tôi xin thông báo đơn hàng <strong style="color: #dc3545;">#{{ $order->order_code }}</strong> của quý khách đã được hủy thành công. Số tiền cần hoàn trả cho quý khách là:</p>

            <div style="text-align: center; margin: 25px 0; padding: 15px; background-color: #fff5f5; border-radius: 8px; border: 1px dashed #dc3545;">
                <span style="font-size: 28px; font-weight: 800; color: #dc3545;">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</span>
            </div>

            <p style="font-size: 15px; color: #444;">Để hệ thống tiến hành hoàn tiền, quý khách vui lòng <strong>Trả lời (Reply)</strong> trực tiếp email này và điền đầy đủ thông tin vào biểu mẫu dưới đây:</p>

            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 5px solid #0d6efd; border-radius: 8px; padding: 25px; margin: 25px 0;">
                <h4 style="margin: 0 0 20px 0; color: #0d6efd; font-size: 16px; text-transform: uppercase;">Thông tin nhận hoàn tiền</h4>
                
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 15px;">
                    <tr>
                        <td width="160" style="padding-bottom: 12px;"><strong>1. Tên Ngân hàng:</strong></td>
                        <td style="padding-bottom: 12px; color: #94a3b8;">...........................................................</td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 12px;"><strong>2. Số Tài khoản:</strong></td>
                        <td style="padding-bottom: 12px; color: #94a3b8;">...........................................................</td>
                    </tr>
                    <tr>
                        <td><strong>3. Tên Chủ tài khoản:</strong></td>
                        <td style="color: #94a3b8;">...........................................................</td>
                    </tr>
                </table>
            </div>

            <div style="background-color: #fffbeb; padding: 12px 15px; border-radius: 6px; margin-bottom: 25px;">
                <p style="margin: 0; font-size: 14px; color: #b45309;">
                    ⏳ <strong>Lưu ý:</strong> Chúng tôi sẽ xử lý lệnh hoàn tiền trong vòng <strong>24h - 48h làm việc</strong> ngay sau khi nhận được phản hồi từ quý khách.
                </p>
            </div>

            <p style="font-size: 15px; margin-bottom: 0; color: #555;">Xin chân thành cảm ơn quý khách đã tin tưởng và đồng hành cùng PBall Store!</p>
        </div>

        <div style="background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #eeeeee;">
            <p style="margin: 0; font-size: 13px; color: #6c757d;">Nếu quý khách cần hỗ trợ gấp, vui lòng liên hệ Hotline:</p>
            <p style="margin: 8px 0 0 0; font-size: 20px; font-weight: bold; color: #1f2937;">0967 854 619</p>
            <p style="margin: 15px 0 0 0; font-size: 12px; color: #adb5bd;">&copy; {{ date('Y') }} PBall Store. All rights reserved.</p>
        </div>

    </div>
</div>
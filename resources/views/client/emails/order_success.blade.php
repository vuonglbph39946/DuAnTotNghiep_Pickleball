<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e6e6e6; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    
    <div style="background-color: #dc3545; padding: 20px; text-align: center; color: #ffffff;">
        <h2 style="margin: 0; font-size: 24px; letter-spacing: 1px;">PBALL STORE</h2>
        <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Xác nhận đơn đặt hàng thành công</p>
    </div>

    <div style="padding: 30px 20px;">
        <p style="font-size: 16px; margin-top: 0;">Xin chào <strong>{{ $order->customer_name ?? 'Quý khách' }}</strong>,</p>
        <p style="font-size: 15px;">Cảm ơn bạn đã tin tưởng và mua sắm tại <strong>PBall Store</strong>. Đơn hàng của bạn đã được hệ thống ghi nhận và đang trong quá trình xử lý.</p>

        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px dashed #ced4da; margin: 25px 0;">
            <h3 style="margin-top: 0; font-size: 16px; color: #495057; border-bottom: 1px solid #e9ecef; padding-bottom: 10px;">Tóm tắt đơn hàng</h3>
            <table style="width: 100%; font-size: 15px;">
                <tr>
                    <td style="padding: 5px 0; color: #6c757d;">Mã đơn hàng:</td>
                    <td style="padding: 5px 0; text-align: right;"><strong style="color: #dc3545;">#{{ $order->order_code }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #6c757d;">Tổng tiền:</td>
                    <td style="padding: 5px 0; text-align: right;"><strong>{{ number_format($order->total_amount) }} VNĐ</strong></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #6c757d;">Thanh toán:</td>
                    <td style="padding: 5px 0; text-align: right;"><strong>{{ strtoupper($order->payment_method) }}</strong></td>
                </tr>
            </table>
        </div>

        <p style="font-size: 15px; text-align: center; margin-bottom: 25px;">Để theo dõi tiến trình giao hàng và xem chi tiết các sản phẩm đã đặt, vui lòng nhấn vào nút bên dưới:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('account.index') }}" style="background-color: #dc3545; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 6px rgba(220,53,69,0.2);">
                Xem Chi Tiết Đơn Hàng
            </a>
        </div>
    </div>

    <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e6e6e6;">
        <p style="margin: 0; font-size: 13px; color: #6c757d;">Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua email này.</p>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: #6c757d;">Trân trọng, <strong>Đội ngũ PBall Store</strong></p>
    </div>

</div>
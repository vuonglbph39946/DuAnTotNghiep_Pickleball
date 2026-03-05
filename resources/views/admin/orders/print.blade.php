<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn / Vận Đơn #{{ $order->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; color: #000; background: #fff; margin: 0; padding: 20px; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px; }
        .header .logo h2 { margin: 0; font-size: 24px; font-weight: 800; text-transform: uppercase; }
        .header .info { text-align: right; }
        .customer-info { margin-bottom: 30px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        table th { background-color: #f8f9fa; font-weight: 600; text-transform: uppercase; font-size: 12px; }
        table td.text-end, table th.text-end { text-align: right; }
        table td.text-center, table th.text-center { text-align: center; }
        .total-section { width: 50%; float: right; }
        .total-section table td { border: none; padding: 8px 0; }
        .total-section table tr.grand-total td { font-size: 18px; font-weight: 700; border-top: 2px solid #000; padding-top: 10px; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .footer-signatures { margin-top: 50px; display: flex; justify-content: space-between; text-align: center; }
        .signature-box { width: 200px; }
        /* CỰC KỲ QUAN TRỌNG: ẨN MỌI THỨ THỪA KHI IN */
        @media print {
            body { padding: 0; background: #fff; }
            .invoice-box { border: none; box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        <div class="logo">
            <h2>PICKLEBALL STORE</h2>
            <p style="margin: 5px 0 0 0; color: #555;">Hệ thống cung cấp vợt chuẩn quốc tế</p>
        </div>
        <div class="info">
            <h3 style="margin: 0 0 5px 0; font-size: 20px;">HÓA ĐƠN BÁN HÀNG</h3>
            <strong>Mã đơn:</strong> #{{ $order->id }}<br>
            <strong>Ngày tạo:</strong> {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : now()->format('d/m/Y') }}<br>
            <strong>Trạng thái:</strong> {{ strtoupper($order->payment_status == 'paid' ? 'Đã thanh toán' : 'Thu hộ COD') }}
        </div>
    </div>

    <div class="customer-info">
        <h4 style="margin: 0 0 10px 0; border-bottom: 1px dashed #ccc; display: inline-block; padding-bottom: 5px;">THÔNG TIN NGƯỜI NHẬN</h4>
        <div style="display: flex; justify-content: space-between;">
            <div>
                <strong>Khách hàng:</strong> {{ $address ? $address->receiver_name : 'User '.$order->user_id }}<br>
                <strong>Số điện thoại:</strong> {{ $address ? $address->phone : 'N/A' }}<br>
                <strong>Địa chỉ:</strong> {{ $address ? $address->address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city : 'N/A' }}
            </div>
            <div style="border: 2px dashed #000; padding: 15px; text-align: center; border-radius: 8px;">
                <strong style="font-size: 16px;">TIỀN THU HỘ (COD)</strong><br>
                <span style="font-size: 24px; font-weight: 800;">
                    {{ $order->payment_status == 'unpaid' ? number_format($order->total_amount) . ' đ' : '0 đ' }}
                </span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">STT</th>
                <th style="width: 45%;">Tên sản phẩm</th>
                <th class="text-center" style="width: 15%;">Số lượng</th>
                <th class="text-end" style="width: 15%;">Đơn giá</th>
                <th class="text-end" style="width: 20%;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @php $sum = 0; @endphp
            @foreach($order->items as $index => $item)
            @php 
                $line = $item->price * $item->quantity; 
                $sum += $line; 
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->product->name ?? 'Sản phẩm' }}</strong><br>
                    <span style="font-size: 12px; color: #666;">SKU: #{{ $item->product_id }}</span>
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($item->price) }} đ</td>
                <td class="text-end fw-bold">{{ number_format($line) }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="total-section">
            <table style="margin: 0;">
                <tr>
                    <td>Cộng tiền hàng:</td>
                    <td class="text-end">{{ number_format($sum) }} đ</td>
                </tr>
                <tr>
                    <td>Phí vận chuyển:</td>
                    <td class="text-end">0 đ</td>
                </tr>
                <tr class="grand-total">
                    <td>TỔNG THANH TOÁN:</td>
                    <td class="text-end">{{ number_format($order->total_amount) }} đ</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer-signatures">
        <div class="signature-box">
            <strong>Người mua hàng</strong><br>
            <i style="font-size: 12px; color: #666;">(Ký, ghi rõ họ tên)</i>
        </div>
        <div class="signature-box">
            <strong>Người giao hàng</strong><br>
            <i style="font-size: 12px; color: #666;">(Ký, ghi rõ họ tên)</i>
        </div>
        <div class="signature-box">
            <strong>Pickleball Store</strong><br>
            <i style="font-size: 12px; color: #666;">(Ký, đóng dấu)</i>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>
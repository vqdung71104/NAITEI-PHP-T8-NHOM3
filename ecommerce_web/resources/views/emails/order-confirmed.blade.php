<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đơn hàng đã được xác nhận</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .order-items {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .order-items th, .order-items td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .order-items th {
            background-color: #007bff;
            color: white;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Đơn hàng đã được xác nhận!</h1>
        </div>

        <p>Xin chào <strong>{{ $order->user->name ?? 'Quý khách' }}</strong>,</p>
        
        <p>Chúng tôi xin thông báo đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>

        <div class="order-info">
            <h3>Thông tin đơn hàng:</h3>
            <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Ngày xác nhận:</strong> {{ $order->confirmed_at ? $order->confirmed_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
            <p><strong>Trạng thái:</strong> <span style="color: green;">Đã xác nhận</span></p>
        </div>

        <h3>Chi tiết đơn hàng:</h3>
        <table class="order-items">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Sản phẩm không tồn tại' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->product->price ?? 0, 0, ',', '.') }}đ</td>
                    <td>{{ number_format($item->quantity * ($item->product->price ?? 0), 0, ',', '.') }}đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: right;">
            <p class="total">Tổng cộng: {{ number_format($order->total_price, 0, ',', '.') }}đ</p>
        </div>

        <div class="order-info">
            <h3>Thông tin giao hàng:</h3>
            <p><strong>Người nhận:</strong> {{ $order->address->name ?? $order->user->name ?? 'N/A' }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->address->ward . ', ' .$order->address->district . ', ' . $order->address->city ?? 'N/A' }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->address->phone_number ?? $order->user->phone ?? 'N/A' }}</p>
        </div>

        <p>Đơn hàng của bạn sẽ được giao trong vòng 2-3 ngày làm việc. Chúng tôi sẽ liên hệ với bạn trước khi giao hàng.</p>

        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc số điện thoại hỗ trợ.</p>

        <div class="footer">
            <p>Cảm ơn bạn đã tin tưởng và mua hàng tại cửa hàng của chúng tôi!</p>
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Email: support@example.com | Hotline: 1900-xxxx</p>
        </div>
    </div>
</body>
</html>
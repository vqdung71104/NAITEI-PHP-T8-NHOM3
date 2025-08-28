<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo doanh thu hàng ngày</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        /* .summary-cards and .card replaced by table-based layout for email compatibility */
        .summary-cards-table {
            width: 100%;
            margin-bottom: 30px;
            border-spacing: 20px 0;
        }
        .card-cell {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 33.33%;
        }
        .card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card .value {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .details {
            margin-top: 30px;
        }
        .details h3 {
            color: #007bff;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .order-table th,
        .order-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        .order-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .order-table tr:hover {
            background-color: #f8f9fa;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status.confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status.shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .no-orders {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        @media (max-width: 600px) {
            .summary-cards {
                flex-direction: column;
            }
            .container {
                padding: 20px;
            }
            .order-table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Báo cáo doanh thu hàng ngày</h1>
            <p style="margin: 10px 0 0 0; color: #6c757d;">{{ $reportDate }}</p>
        </div>

        <div class="summary-cards">
            <div class="card">
                <h3>Tổng đơn hàng</h3>
                <p class="value">{{ number_format($totalOrders) }}</p>
            </div>
            <div class="card">
                <h3>Tổng doanh thu</h3>
                <p class="value">{{ number_format($totalRevenue) }} VNĐ</p>
            </div>
        </div>

        @if($orderDetails && count($orderDetails) > 0)
            <div class="details">
                <h3>Chi tiết đơn hàng ({{ count($orderDetails) }} đơn)</h3>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Giá trị</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderDetails as $order)
                            <tr>
                                <td>#{{ $order['id'] }}</td>
                                <td>{{ $order['customer_name'] }}</td>
                                <td>{{ number_format($order['total_price']) }} VNĐ</td>
                                <td>
                                    <span class="status {{ $order['status'] }}">
                                        @switch($order['status'])
                                            @case('confirmed')
                                                Đã xác nhận
                                                @break
                                            @case('pending')
                                                Chờ xử lý
                                                @break
                                            @case('shipped')
                                                Đã giao
                                                @break
                                            @case('delivered')
                                                Hoàn thành
                                                @break
                                            @default
                                                {{ ucfirst($order['status']) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>{{ $order['created_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-orders">
                <p>📝 Không có đơn hàng nào trong ngày {{ $reportDate }}</p>
            </div>
        @endif

        <div class="footer">
            <p>
                <strong>📧 Email tự động</strong><br>
                Báo cáo này được gửi tự động hàng ngày lúc 8:00 AM<br>
                © {{ date('Y') }} {{ config('app.name', 'E-commerce') }}
            </p>
        </div>
    </div>
</body>
</html>

@extends('layouts.user')

@section('title', 'Theo dõi đơn hàng')

@section('content')

@vite(['resources/css/orders/track.css', 'resources/js/orders/track.js'])
<script>
    window.ordersData = @json($orders);
</script>
<body>
    <div class="track-page-header">
        <div class="header-content">
            <h1 class="header-title">Lịch Sử Đơn Hàng</h1>
            <p class="header-subtitle">Theo dõi và quản lý các đơn hàng của bạn</p>
        </div>
    </div>

    <div class="main-container">
        <div id="successNotification" class="success-notification" style="display: none;">
            <div class="notification-text">Thông tin đơn hàng đã được cập nhật thành công.</div>
        </div>

        <div class="filters-section">
            <h3 class="filters-title">Tìm kiếm đơn hàng</h3>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Trạng thái đơn hàng</label>
                    <select id="statusFilter" class="filter-input">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="processing">Đang chuẩn bị</option>
                        <option value="shipped">Đang giao hàng</option>
                        <option value="delivered">Đã giao hàng</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Từ ngày</label>
                    <input type="date" id="dateFrom" class="filter-input">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Đến ngày</label>
                    <input type="date" id="dateTo" class="filter-input">
                </div>
            </div>
        </div>

        
        <div id="ordersContainer" class="orders-grid">
            @forelse($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-meta">
                            <div class="order-id-section">
                                <div class="order-id">#{{ $order->id }}</div>
                                <div class="order-date">{{ $order->created_at->format('d/m/Y') }}</div>
                            </div>
                            <div class="order-status status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </div>
                        </div>
                    </div>

                    <div class="order-content">
                        <div class="books-section">
                            <div class="books-title">Sách đã đặt mua</div>
                            @foreach($order->orderItems as $item)
                                <div class="book-item">
                                    <span class="book-name">{{ $item->product->name }}</span>
                                    <span class="book-quantity">{{ $item->quantity }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="order-details">
                            <div class="detail-card">
                                <div class="detail-label">Địa chỉ giao hàng</div>
                                <div class="detail-value">{{ $order->address->details ?? 'Chưa có' }}</div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-label">Số điện thoại</div>
                                <div class="detail-value">{{ $order->address->phone_number ?? 'Chưa có' }}</div>
                            </div>
                        </div>

                        <div class="total-section">
                            <div class="total-label">Tổng giá trị</div>
                            <div class="total-amount">{{ number_format($order->total_price, 0, ',', '.') }}₫</div>
                        </div>
                    </div>
                </div>
            @empty
                <div id="emptyState" class="empty-state">
                    <div class="empty-icon">📚</div>
                    <h2 class="empty-title">Chưa có đơn hàng</h2>
                    <p class="empty-message">Bạn chưa có đơn hàng nào. Hãy khám phá bộ sưu tập sách đặc biệt của chúng tôi.</p>
                    <button class="empty-action" onclick="window.location.href='{{ route('home') }}'">Khám phá sách</button>
                </div>
            @endforelse
        </div>

        


        <div id="emptyState" class="empty-state" style="display: none;">
            <div class="empty-icon">📚</div>
            <h2 class="empty-title">Chưa có đơn hàng</h2>
            <p class="empty-message">Bạn chưa có đơn hàng nào. Hãy khám phá bộ sưu tập sách đặc biệt của chúng tôi.</p>
            <button class="empty-action" onclick="window.location.href='#'">Khám phá sách</button>
        </div>
    </div>
</body>
@endsection
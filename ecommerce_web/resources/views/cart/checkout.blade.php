@extends('layouts.user')

@section('title', 'Thanh toán')

@section('content')

@vite(['resources/css/cart/checkout.css', 'resources/js/cart/checkout.js'])
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Đặt hàng</div>
        </div>

        <div class="checkout-grid">
            <div class="form-section">
                <div class="section-title">Thông tin giao hàng</div>
                <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
    @csrf

    {{-- Tên người nhận --}}
    <div class="form-row">
        <div class="form-group">
            <label for="firstName">Họ</label>
            <input type="text" id="firstName" name="firstName" required placeholder="Nhập họ của người nhận">
        </div>
        <div class="form-group">
            <label for="lastName">Tên</label>
            <input type="text" id="lastName" name="lastName" required placeholder="Nhập tên của người nhận">
        </div>
    </div>

    {{-- Email --}}
    <div class="form-group">
        <label for="email">Địa chỉ email</label>
        <input type="email" id="email" name="email" required placeholder="your.email@domain.com">
    </div>

    {{-- Số điện thoại --}}
    <div class="form-group">
        <label for="phone">Số điện thoại</label>
        <input type="tel" id="phone" name="phone_number" required placeholder="+84 123 456 789">
    </div>

    {{-- Địa chỉ chi tiết --}}
    <div class="form-group">
        <label for="address">Địa chỉ giao hàng</label>
        <input type="text" id="address" name="details" required placeholder="Số nhà, tên đường, phường/xã">
    </div>

    {{-- Thông tin địa phương (ward, district, city, postal_code, country) --}}
    <div class="form-row">
        <div class="form-group">
            <input type="text" name="ward" placeholder="Phường/Xã" required>
        </div>
        <div class="form-group">
            <input type="text" name="district" placeholder="Quận/Huyện" required>
        </div>
        <div class="form-group">
            <input type="text" name="city" placeholder="Tỉnh/Thành phố" required>
        </div>
        <div class="form-group">
            <input type="text" name="postal_code" placeholder="Mã bưu chính" required>
        </div>
        <div class="form-group">
            <input type="text" name="country" placeholder="Quốc gia" required value="Vietnam">
        </div>
    </div>

    {{-- Ghi chú --}}
    <div class="form-group">
        <label for="notes">Ghi chú đặc biệt</label>
        <textarea id="notes" name="notes" placeholder="Yêu cầu đặc biệt về thời gian giao hàng, đóng gói..."></textarea>
    </div>

    {{-- is_default --}}
    <input type="hidden" name="is_default" value="1">

    {{-- Phương thức thanh toán --}}
    <div class="payment-section">
        <div class="section-title">Phương thức thanh toán</div>
        <div class="payment-method">
            <div class="payment-header">
                <div class="payment-icon">COD</div>
                <div class="payment-details">
                    <h3>Thanh toán khi nhận hàng</h3>
                    <p>Thanh toán bằng tiền mặt khi giao hàng</p>
                </div>
            </div>
            <div class="payment-benefits">
                <div>Kiểm tra kỹ sản phẩm trước khi thanh toán</div>
                <div>Hoàn toàn miễn phí, không phát sinh thêm chi phí</div>
                <div>Đảm bảo an toàn tuyệt đối cho khách hàng</div>
                <div>Hỗ trợ đổi trả trong vòng 7 ngày</div>
            </div>
        </div>
    </div>
</form>

            </div>

            <div class="order-summary">
                <div class="section-title">Tóm tắt đơn hàng</div>

                @php
                    $subtotal = 0;
                    $shipping = 30000;
                @endphp

                @foreach($cartItems as $item)
                    @php $subtotal += $item->product->price * $item->quantity; @endphp
                    <div class="product-item">
                        <div class="product-image">
                            <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/80x100' }}" alt="{{ $item->product->name }}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div class="product-info">
                            <div class="product-name">{{ $item->product->name }}</div>
                            <div class="product-variant">Số lượng: {{ $item->quantity }}</div>
                            {{-- Nếu có variant khác thì hiển thị --}}
                        </div>
                        <div class="product-price">{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}₫</div>
                    </div>
                @endforeach

                <div class="summary-divider"></div>

                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span>{{ number_format($shipping, 0, ',', '.') }}₫</span>
                </div>
                <div class="summary-row total">
                    <span>Tổng thanh toán</span>
                    <span>{{ number_format($subtotal + $shipping, 0, ',', '.') }}₫</span>
                </div>

                <button type="submit" class="submit-btn" onclick="document.getElementById('checkoutForm').submit();">
                    Hoàn tất đơn hàng
                </button>

                <div class="security-note">
                    Được bảo mật bởi SSL 256-bit
                </div>
            </div>
        </div>
    </div>
</body>
@endsection

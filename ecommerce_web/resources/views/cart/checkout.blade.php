@extends('layouts.user')

@section('title', 'Thanh toán')

@section('content')

@vite(['resources/css/cart/checkout.css', 'resources/js/cart/checkout.js'])

<div class="container">
    <div class="header">
        <div class="logo">Đặt hàng</div>
    </div>
    <div class="checkout-grid">
        <div class="form-section">
            <div class="section-title">Thông tin giao hàng</div>

            <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST" novalidate>
                @csrf

                {{-- Chọn kiểu địa chỉ --}}
                <div class="form-group">
                    <label for="address_option">Chọn địa chỉ</label>
                    <select id="address_option" name="address_option" class="form-control" required
                        data-has-addresses="{{ ($addresses ?? collect())->count() > 0 ? '1' : '0' }}">
                        <option value="">-- Chọn địa chỉ --</option>
                        <option value="existing"
                            {{ old('address_option', (($addresses ?? collect())->count() ? 'existing' : '')) === 'existing' ? 'selected' : '' }}>
                            Địa chỉ đã lưu
                        </option>
                        <option value="new" {{ old('address_option') === 'new' ? 'selected' : '' }}>
                            Nhập địa chỉ mới
                        </option>
                    </select>
                </div>

                {{-- Existing address --}}
                <div class="form-group" id="existing_address_block" style="display:none;">
                    <label for="address_id">Địa chỉ đã lưu</label>
                    <select id="address_id" name="address_id" class="form-control">
                        <option value="">-- Chọn địa chỉ --</option>
                        @foreach(($addresses ?? collect()) as $address)
                            <option value="{{ $address->id }}"
                                {{ (string)old('address_id') === (string)$address->id ? 'selected' : '' }}>
                                {{ $address->details }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }} ({{ $address->phone_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- New address --}}
                <div id="new_address_form" style="display:none;">
                    <div class="form-group">
                        <label for="full_name">Họ và tên</label>
                        <input type="text" id="full_name" name="full_name" class="form-control"
                               value="{{ old('full_name') }}" placeholder="Nhập họ và tên">
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Số điện thoại</label>
                        <input type="tel" id="phone_number" name="phone_number" class="form-control"
                               value="{{ old('phone_number') }}" placeholder="+84xxxxxxxxx hoặc 0xxxxxxxxx"
                               inputmode="tel" autocomplete="tel">
                    </div>
                    <div class="form-group">
                        <label for="details">Địa chỉ chi tiết</label>
                        <input type="text" id="details" name="details" class="form-control"
                               value="{{ old('details') }}" placeholder="Số nhà, tên đường, phường/xã">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="ward" name="ward" class="form-control"
                                   value="{{ old('ward') }}" placeholder="Phường/Xã">
                        </div>
                        <div class="form-group">
                            <input type="text" id="district" name="district" class="form-control"
                                   value="{{ old('district') }}" placeholder="Quận/Huyện">
                        </div>
                        <div class="form-group">
                            <input type="text" id="city" name="city" class="form-control"
                                   value="{{ old('city') }}" placeholder="Tỉnh/Thành phố">
                        </div>
                        <div class="form-group">
                            <input type="text" id="postal_code" name="postal_code" class="form-control"
                                   value="{{ old('postal_code') }}" placeholder="Mã bưu chính (không bắt buộc)">
                        </div>
                        <div class="form-group">
                            <input type="text" id="country" name="country" class="form-control"
                                   value="{{ old('country', 'Vietnam') }}" placeholder="Quốc gia">
                        </div>
                    </div>
                    <button type="button" id="confirmAddressBtn" class="btn btn-primary mt-2">
                        Xác nhận địa chỉ
                    </button>
                </div>

                {{-- Ghi chú --}}
                <div class="form-group">
                    <label for="notes">Ghi chú (tùy chọn)</label>
                    <textarea id="notes" name="notes" class="form-control" placeholder="Thêm ghi chú cho đơn hàng">{{ old('notes') }}</textarea>
                </div>

                <input type="hidden" name="payment_method" value="COD">

                <button type="submit" class="submit-btn">Hoàn tất đơn hàng</button>
            </form>
        </div>

        <div class="order-summary">
            <div class="section-title">Tóm tắt đơn hàng</div>

            @php $subtotal = 0; $shipping = $shipping ?? 30000; @endphp

            @foreach($cartItems as $item)
                @php $subtotal += ($item->product->price ?? 0) * $item->quantity; @endphp
                <div class="product-item">
                    <div class="product-image">
                        <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/80x100' }}"
                             alt="{{ $item->product->name }}"
                             style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="product-info">
                        <div class="product-name">{{ $item->product->name }}</div>
                        <div class="product-variant">Số lượng: {{ $item->quantity }}</div>
                    </div>
                    <div class="product-price">
                        {{ number_format(($item->product->price ?? 0) * $item->quantity, 0, ',', '.') }}₫
                    </div>
                </div>
            @endforeach

            <div class="summary-divider"></div>

            <div class="summary-row">
                <span>Tạm tính</span>
                <span id="subtotal" data-value="{{ $subtotal }}">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
            </div>
            <div class="summary-row">
                <span>Phí vận chuyển</span>
                <span id="shipping_fee" data-value="{{ $shipping }}">{{ number_format($shipping, 0, ',', '.') }}₫</span>
            </div>
            <div class="summary-row total">
                <span>Tổng thanh toán</span>
                <span id="total_price" data-subtotal="{{ $subtotal }}" data-shipping="{{ $shipping }}">
                    {{ number_format($subtotal + $shipping, 0, ',', '.') }}₫
                </span>
            </div>

            <div class="security-note">Được bảo mật bởi SSL 256-bit</div>
        </div>
    </div>
</div>
@endsection

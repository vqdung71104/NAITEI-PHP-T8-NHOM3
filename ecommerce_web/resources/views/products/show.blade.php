@extends('layouts.user')

@section('title', $product->name)

@section('content')

@vite(['resources/css/products/show.css', 'resources/js/products/show.js'])

<body>
    <div class="container">
        <a href="{{ route('products.viewall') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }} back">← Quay lại</a>

        <div class="product">
            <div class="image">
                <div style="position: relative;">
                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/600x400?text=No+Image' }}" 
                        alt="{{ $product->name }}">
                    <div class="stock">{{ $product->stock ?? 0 }} cuốn</div>
                </div>
            </div>

            <div class="info">
                <h1>{{ $product->name }}</h1>
                <div class="price">{{ number_format($product->price,0,',','.') }}đ</div>
                <p class="description">
                    {{ $product->description ?? 'Chưa có mô tả cho sản phẩm này.' }}
                </p>

                <div class="purchase">
                    <div class="quantity-row">
                        <span>Số lượng</span>
                        <div class="quantity">
                            <button type="button" class="quantity-btn minus" data-product-id="{{ $product->id }}">−</button>
                            <input type="number" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock ?? 1 }}" readonly>
                            <button type="button" class="quantity-btn plus" data-product-id="{{ $product->id }}">+</button>
                        </div>
                    </div>
                    @auth
                        <form id="add-to-cart-form-{{ $product->id }}" action="{{ route('cart.add') }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="quantity-input-{{ $product->id }}" value="1">
                            <button type="submit" class="add-cart">Thêm vào giỏ hàng</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="add-cart">Thêm vào giỏ hàng</a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="reviews">
            <h2 class="section-title">Đánh giá</h2>

            {{-- Thông báo --}}
            @if (session('success'))
                <div id="success" class="success">{{ session('success') }}</div>
            @endif

            {{-- Form thêm review --}}
            @auth
                <form action="{{ route('reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ tên</label>
                            <input type="text" value="{{ Auth::user()->name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <div class="form-group full">
                            <label>Nhận xét</label>
                            <textarea name="content" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Số sao</label>
                            <select name="rating" required>
                                <option value="">-- Chọn --</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} ⭐</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="submit">Gửi đánh giá</button>
                </form>
            @else
                <p><a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá.</p>
            @endauth

            {{-- Danh sách review --}}
            <div class="review-list" id="reviews">
                @forelse ($product->reviews()->with('user')->latest()->get() as $review)
                    <div class="review">
                        <div class="review-header">
                            <span class="review-name">{{ $review->user->name ?? 'Người dùng' }}</span>
                            <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="review-content">
                            {{ $review->content }}
                            <div>⭐ {{ $review->rating }}/5</div>
                        </div>
                    </div>
                @empty
                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
@if(session('success'))
    <script>
        alert({!! json_encode(session('success')) !!});
    </script>
@endif
@endsection

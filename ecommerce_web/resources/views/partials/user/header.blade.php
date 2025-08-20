@vite(['resources/css/partials/user/header.css', 'resources/js/partials/user/header.js'])
<meta name="user-logged-in" content="{{ auth()->check() ? '1' : '0' }}">
<body>
    <header class="page-header">
    <nav>
            <ul id="navMenu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{__("Trang chủ")}}</a></li>
                <li><a href="{{ route('products.viewall') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">{{__("Sản phẩm")}}</a></li>
                <li><a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">{{__("Giỏ hàng")}}</a></li>
                <li><a href="{{ route('orders.track') }}" class="{{ request()->routeIs('orders.track') ? 'active' : '' }}">{{__("Theo dõi đơn hàng")}}</a></li>
                <li><a
                            href="{{ url('/language/en') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            English
                        </a></li>
                        <li><a
                            href="{{ url('/language/vi') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Vietnamese
                        </a></li>
                <li class="user-menu">
                    @auth
                        <div id="authUser">
                            <a href="#" class="btn btn-primary" onclick="toggleDropdown(event)">{{__("Xem Profile")}}</a>
                            <div class="user-dropdown" id="userDropdown">
                                <a href="{{ route('home') }}">{{__("Profile")}}</a>
                                <a href="{{ route('orders.track') }}">{{__("Đơn hàng")}}</a>
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{__("Đăng xuất")}}
                                </a>
                            </div>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    @endauth

                    <!-- Người dùng chưa đăng nhập -->
                    @guest
                        <div id="guestUser">
                            <a href="{{ route('login') }}" class="btn btn-secondary login-btn">{{__("Đăng nhập")}}</a>
                        </div>
                    @endguest
                </li>
        </ul>
            <button class="mobile-toggle" onclick="toggleMenu()">☰</button>
    </nav>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</header>
</body>
<header class="header">
    <div class="header__inner">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="header__logo">
        </a>

        @unless (in_array(Route::currentRouteName(), ['login', 'register', 'verification.notice']))
            <form action="#" method="GET" class="header__search-form">
                <input type="text" name="keyword" placeholder="なにをお探しですか？" class="header__search-input">
            </form>

            <nav class="header__nav">
                @auth
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="header__link">ログアウト</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="header__link">ログイン</a>
                @endguest

                <a href="{{ url('/mypage') }}" class="header__link">マイページ</a>
                @auth
                    <a href="{{ url('/sell') }}" class="header__button">出品</a>
                @else
                    <a href="{{ route('login') }}" class="header__button">出品</a>
                @endauth
            </nav>
        @endunless
    </div>
</header>
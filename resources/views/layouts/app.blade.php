<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'coachtech 勤怠管理アプリ')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/common.css') }}">
    @stack('styles')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <img src="{{ asset('images/logo.svg')}}" alt="COACHTECHロゴ">
            </div>

            {{-- このファイルの全てのルートも直すし、以下のボタンたちもheaderType 方針に沿って作り直しする

            @unless (request()->routeIs('login', 'register', 'register.verify', 'verification.*'))
                <div class="header__right">


                    <a class="header__button" href="{{ route('mypage', [], false) }}">勤怠</a>
                    <a class="header__button header__button--primary" href="{{ route('sell.create') }}">勤怠一覧</a>
                    <a class="header__button header__button--primary" href="{{ route('sell.create') }}">申請</a>


                    <a class="header__button" href="{{ route('mypage', [], false) }}">今月の出勤一覧</a>
                    <a class="header__button header__button--primary" href="{{ route('sell.create') }}">申請一覧</a>


                    <form class="header__logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header__button">ログアウト</button>
                    </form>
                </div>
            @endunless
            --}}
        </div>
        <form class="header__logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header__button">ログアウト</button>
                    </form>
    </header>

    <main class="main">
        @yield('content')
    </main>

</body>
</html>

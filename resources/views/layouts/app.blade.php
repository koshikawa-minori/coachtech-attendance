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
    @php
        $headerType = $headerType ?? 'user';
    @endphp

    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                    <img src="{{ asset('images/logo.svg')}}" alt="COACHTECHロゴ">
            </div>
            @if ($headerType === 'auth')
                {{-- 右側は何も出さない --}}
            @elseif ($headerType === 'user')
                <div class="header__right">
                    <a class="header__button header__button--primary" href="{{ route('attendance.show') }}">勤怠</a>
                    <a class="header__button header__button--primary" href="{{ route('attendance.index') }}">勤怠一覧</a>
                    <a class="header__button header__button--primary" href="{{ route('requests.index') }}">申請</a>

                    <form class="header__logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <input type="hidden" name="logout_type" value="user">
                        <button type="submit" class="header__button">ログアウト</button>
                    </form>
                </div>
            @elseif ($headerType === 'user_clock_out')
                <div class="header__right">
                    <a class="header__button header__button--primary" href="{{ route('attendance.index') }}">今月の出勤一覧</a>
                    <a class="header__button header__button--primary" href="{{ route('requests.index') }}">申請一覧</a>

                    <form class="header__logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <input type="hidden" name="logout_type" value="user">
                        <button type="submit" class="header__button">ログアウト</button>
                    </form>
                </div>
            @elseif ($headerType === 'admin')
                <div class="header__right">
                    <a class="header__button header__button--primary" href="{{ route('admin.attendance.index') }}">勤怠一覧</a>
                    <a class="header__button header__button--primary" href="{{ route('staff.list.index') }}">スタッフ一覧</a>
                    <a class="header__button header__button--primary" href="{{ route('admin.requests.index') }}">申請一覧</a>

                    <form class="header__logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <input type="hidden" name="logout_type" value="admin">
                        <button type="submit" class="header__button">ログアウト</button>
                    </form>
                </div>
            @endif
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>

</body>
</html>

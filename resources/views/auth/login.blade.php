<!doctype html>
<html><body>
<h1>ログイン</h1>
@if ($errors->any()) <div>{{ $errors->first() }}</div> @endif
<form method="POST" action="{{ route('login') }}">
  @csrf
  <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label><br>
  <label>Password <input type="password" name="password" required></label><br>
  <label><input type="checkbox" name="remember"> Remember me</label><br>
  <button type="submit">ログイン</button>
</form>
<a>パスワードをお忘れですか？</a>
</body></html>

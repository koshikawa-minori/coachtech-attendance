<!doctype html>
<html><body>
<h1>会員登録</h1>
@if ($errors->any()) <div>{{ $errors->first() }}</div> @endif
<form method="POST" action="{{ route('register') }}">
  @csrf
  <label>お名前 <input type="text" name="name" value="{{ old('name') }}" required></label><br>
  <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label><br>
  <label>Password <input type="password" name="password" required></label><br>
  <label>Confirm <input type="password" name="password_confirmation" required></label><br>
  <button type="submit">登録</button>
</form>
</body></html>

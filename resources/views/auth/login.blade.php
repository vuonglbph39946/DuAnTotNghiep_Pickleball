<h2>Đăng nhập</h2>

@if(session('error'))
<p>{{session('error')}}</p>
@endif

<form method="POST" action="/login">

@csrf

Email
<input type="email" name="email">

Password
<input type="password" name="password">

<button type="submit">Đăng nhập</button>

</form>

<a href="/register">Đăng ký</a>
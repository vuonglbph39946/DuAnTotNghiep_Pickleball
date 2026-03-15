<h2>Đăng ký</h2>

<form method="POST" action="/register">

@csrf

Tên
<input type="text" name="name">

Email
<input type="email" name="email">

Số điện thoại
<input type="text" name="phone">

Password
<input type="password" name="password">

<button type="submit">Đăng ký</button>

</form>

<a href="/login">Đăng nhập</a>
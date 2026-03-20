@extends('client.layouts.app')
@section('title', 'Đặt hàng thành công')
@section('content')
<div class="container text-center p-t-100 p-b-100">
    <i class="fa-solid fa-circle-check text-success" style="font-size: 80px;"></i>
    <h2 class="m-t-20 m-b-20">Đặt hàng thành công!</h2>
    <p>Mã đơn hàng: <strong>#{{ $order->order_code }}</strong></p>
    <p class="m-b-30">Chúng tôi đã gửi email xác nhận đến địa chỉ <strong>{{ $order->customer_email }}</strong>.</p>
    
    {{-- LOGIC NÚT BẤM KỲ DIỆU Ở ĐÂY --}}
    @if(Auth::check())
        <a href="{{ route('account.index') }}" class="btn btn-dark p-lr-30 p-tb-10">Xem chi tiết đơn hàng</a>
    @else
        {{-- Truyền tham số redirect để login xong tự bay về trang Account --}}
        <a href="{{ route('login', ['redirect' => route('account.index')]) }}" class="btn btn-danger p-lr-30 p-tb-10">Đăng nhập để xem đơn hàng</a>
    @endif
    <a href="{{ route('home') }}" class="btn btn-outline-secondary p-lr-30 p-tb-10 m-l-10">Tiếp tục mua sắm</a>
</div>
@endsection
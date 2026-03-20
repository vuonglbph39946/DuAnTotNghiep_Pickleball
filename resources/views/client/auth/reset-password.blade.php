@extends('client.layouts.app')

@section('title', 'Tạo mật khẩu mới - PBall Store')

@section('content')
<div class="container">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
            Trang chủ
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <span class="stext-109 cl4">
            Tạo mật khẩu mới
        </span>
    </div>
</div>

<section class="bg0 p-t-50 p-b-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="p-t-20 p-b-20">
                    <h4 class="mtext-105 cl2 txt-center p-b-30" style="font-weight: 400; text-transform: uppercase;">
                        TẠO MẬT KHẨU MỚI
                    </h4>

                    <form method="POST" action="{{ route('password.update') }}" id="reset-form">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="p-b-20 pos-relative">
                            <div class="stext-102 cl2 p-b-5" style="font-weight: 600;">Email <span class="text-danger">*</span></div>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8 @error('email') bor-red @enderror" type="email" name="email" value="{{ $email ?? old('email') }}" readonly style="background-color: transparent; cursor: not-allowed; color: #888;">
                            @error('email')
                                <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="p-b-20 pos-relative">
                            <div class="stext-102 cl2 p-b-5" style="font-weight: 600;">Mật khẩu mới <span class="text-danger">*</span></div>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8 @error('password') bor-red @enderror" type="password" name="password" placeholder="Nhập mật khẩu mới">
                            @error('password')
                                <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="p-b-30 pos-relative">
                            <div class="stext-102 cl2 p-b-5" style="font-weight: 600;">Xác nhận mật khẩu <span class="text-danger">*</span></div>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới">
                        </div>

                        <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer">
                            Lưu mật khẩu mới
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .bor-red { border: 1px solid #dc3545 !important; }
    input:-webkit-autofill { -webkit-box-shadow: 0 0 0 30px white inset !important; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('submit', '#reset-form', function(e) {
            e.preventDefault(); 
            let isValid = true;
            $('.js-err-msg, .backend-err').remove(); 
            $('input').removeClass('bor-red');

            let pass = $('input[name="password"]');
            if(pass.val().length < 6) { showError(pass, 'Mật khẩu phải có ít nhất 6 ký tự.'); isValid = false; }

            let passConfirm = $('input[name="password_confirmation"]');
            if(passConfirm.val() !== pass.val() || !passConfirm.val().trim()) { showError(passConfirm, 'Xác nhận mật khẩu không khớp.'); isValid = false; }

            if(isValid) this.submit();
        });

        $('input').on('input', function() {
            $(this).removeClass('bor-red');
            $(this).parent().find('.js-err-msg, .backend-err').remove();
        });

        function showError(input, msg) {
            input.addClass('bor-red');
            input.after('<span class="stext-102 js-err-msg" style="color: #dc3545; display: block; margin-top: 5px;">'+msg+'</span>');
        }
    });
</script>
@endpush
@endsection
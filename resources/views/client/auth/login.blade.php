@extends('client.layouts.app')

@section('title', 'Đăng nhập tài khoản - PBall Store')

@section('content')
<div class="container p-t-80 p-b-100">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="p-t-50 p-b-50">
                <h4 class="mtext-105 cl2 txt-center p-b-10" style="font-size: 26px; font-weight: 500; text-transform: uppercase;">
                    ĐĂNG NHẬP TÀI KHOẢN
                </h4>
                <p class="stext-111 cl6 txt-center p-b-30" style="font-size: 15px;">
                    Bạn chưa có tài khoản ? <a href="{{ route('register') }}" class="hov-cl1 trans-04" style="color: #e65540;">Đăng ký tại đây</a>
                </p>

                @if(session('success'))
                    <div class="alert alert-success text-center" style="font-size: 14px; border-radius: 3px;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf
                    
                    <div class="p-b-20 pos-relative">
                        <label class="stext-102 cl3" style="font-weight: 700; color: #111;">Email <span style="color: red;">*</span></label>
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('email') bor-red @else bor8 @enderror" type="text" name="email" value="{{ old('email') }}" placeholder="Email" style="height: 45px; border-radius: 3px;">
                        @error('email')
                            <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-b-10 pos-relative">
                        <label class="stext-102 cl3" style="font-weight: 700; color: #111;">Mật khẩu <span style="color: red;">*</span></label>
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('password') bor-red @else bor8 @enderror" type="password" name="password" placeholder="Mật khẩu" style="height: 45px; border-radius: 3px;">
                        @error('password')
                            <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex-w flex-sb-m p-b-20">
                        <span class="stext-111 cl6" style="font-size: 13px;">
                            Quên mật khẩu? Nhấn vào <a href="{{ route('password.request') }}" class="hov-cl1 trans-04" style="color: #007bff;">đây</a>
                        </span>
                    </div>

                    <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" style="height: 50px; border-radius: 3px; font-weight: bold; background-color: #000;">
                        Đăng nhập
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bor-red { border: 1px solid #dc3545 !important; }
    
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('submit', '#login-form', function(e) {
            e.preventDefault(); // KHÓA FORM

            let isValid = true;
            $('.js-err-msg, .backend-err').remove(); 
            $('input').removeClass('bor-red');

            let email = $('input[name="email"]');
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if(!email.val().trim()) {
                showError(email, 'Vui lòng nhập email.'); isValid = false;
            } else if(!emailRegex.test(email.val())) {
                showError(email, 'Email không đúng định dạng.'); isValid = false;
            }

            let pass = $('input[name="password"]');
            if(!pass.val().trim()) {
                showError(pass, 'Vui lòng nhập mật khẩu.'); isValid = false;
            }

            if(isValid) {
                this.submit();
            }
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
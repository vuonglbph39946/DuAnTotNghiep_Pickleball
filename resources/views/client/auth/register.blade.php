@extends('client.layouts.app')

@section('title', 'Đăng ký tài khoản - PBall Store')

@section('content')
<div class="container p-t-80 p-b-100">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="p-t-50 p-b-50">
                <h4 class="mtext-105 cl2 txt-center p-b-10" style="font-size: 26px; font-weight: 500; text-transform: uppercase;">
                    ĐĂNG KÝ TÀI KHOẢN
                </h4>
                <p class="stext-111 cl6 txt-center p-b-30" style="font-size: 15px;">
                    Bạn đã có tài khoản ? <a href="{{ route('login') }}" class="hov-cl1 trans-04" style="color: #e65540;">Đăng nhập tại đây</a>
                </p>

                <h5 class="stext-105 cl2 txt-center p-b-30" style="font-size: 18px; font-weight: 500; text-transform: uppercase;">
                    THÔNG TIN CÁ NHÂN
                </h5>

                <form method="POST" action="{{ route('register') }}" id="register-form">
                    @csrf
                    
                    <div class="p-b-20 pos-relative">
                        <label class="stext-102 cl3" style="font-weight: 700; color: #111;">Họ và tên <span style="color: red;">*</span></label>
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('name') bor-red @else bor8 @enderror" type="text" name="name" value="{{ old('name') }}" placeholder="Nhập họ và tên" style="height: 45px; border-radius: 3px;">
                        @error('name')
                            <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-b-20 pos-relative">
                        <label class="stext-102 cl3" style="font-weight: 700; color: #111;">Số điện thoại <span style="color: red;">*</span></label>
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('phone') bor-red @else bor8 @enderror" type="text" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại" style="height: 45px; border-radius: 3px;">
                        @error('phone')
                            <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-b-20 pos-relative">
                        <label class="stext-102 cl3" style="font-weight: 700; color: #111;">Email <span style="color: red;">*</span></label>
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('email') bor-red @else bor8 @enderror" type="text" name="email" value="{{ old('email') }}" placeholder="Email" style="height: 45px; border-radius: 3px; {{ old('email') ? 'background-color: #e8f0fe;' : '' }}">
                        @error('email')
                            <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-b-20 pos-relative">
                        <label class="stext-102 cl3" style="font-weight: 700; color: #111;">Mật khẩu <span style="color: red;">*</span></label>
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('password') bor-red @else bor8 @enderror" type="password" name="password" placeholder="Mật khẩu" style="height: 45px; border-radius: 3px;">
                        @error('password')
                            <span class="stext-102 backend-err" style="color: #dc3545; display: block; margin-top: 5px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-b-30 pos-relative">
                        <label class="stext-102 cl3" style="font-weight: 700; color: #111;">Xác nhận mật khẩu <span style="color: red;">*</span></label>
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" style="height: 45px; border-radius: 3px;">
                    </div>

                    <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" style="height: 50px; border-radius: 3px; font-weight: bold; background-color: #000;">
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bor-red { border: 1px solid #dc3545 !important; }
   
    input:-webkit-autofill { -webkit-box-shadow: 0 0 0 30px #e8f0fe inset !important; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('submit', '#register-form', function(e) {
            e.preventDefault(); // KHÓA FORM NGAY LẬP TỨC

            let isValid = true;
            $('.js-err-msg, .backend-err').remove(); 
            $('input').removeClass('bor-red');

            let name = $('input[name="name"]');
            if(!name.val().trim()) { showError(name, 'Vui lòng nhập họ và tên.'); isValid = false; }

            let phone = $('input[name="phone"]');
            let phoneRegex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/;
            if(!phone.val().trim() || !phoneRegex.test(phone.val())) {
                showError(phone, 'Số điện thoại không hợp lệ (Ví dụ: 0987654321).'); isValid = false;
            }

            let email = $('input[name="email"]');
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!email.val().trim() || !emailRegex.test(email.val())) {
                showError(email, 'Email không đúng định dạng.'); isValid = false;
            }

            let pass = $('input[name="password"]');
            if(pass.val().length < 6) {
                showError(pass, 'Mật khẩu phải có ít nhất 6 ký tự.'); isValid = false;
            }

            let passConfirm = $('input[name="password_confirmation"]');
            if(passConfirm.val() !== pass.val()) {
                showError(passConfirm, 'Xác nhận mật khẩu không khớp.'); isValid = false;
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
@extends('client.layouts.app')

@section('title', 'Đăng nhập Quản Trị Viên - PBall Store')

@section('content')
<div class="container p-t-80 p-b-100">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="p-t-50 p-b-50">
                <h4 class="mtext-105 cl2 txt-center p-b-10" style="font-size: 26px; font-weight: 500; text-transform: uppercase;">
                    ĐĂNG NHẬP QUẢN TRỊ
                </h4>
                <p class="stext-111 cl6 txt-center p-b-30" style="font-size: 15px;">
                    Khu vực dành riêng cho Ban Quản Trị
                </p>

                @if(session('error'))
                    <div class="alert alert-danger text-center" style="font-size: 14px; border-radius: 3px; margin-bottom: 20px;">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger text-center" style="font-size: 14px; border-radius: 3px; margin-bottom: 20px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" id="login-form">
                    @csrf
                    
                    <div class="p-b-20">
                        <label class="stext-102 cl3" for="email">Email quản trị</label>
                        <input class="size-111 bor8 stext-102 cl2 p-lr-20" type="email" name="email" id="email" placeholder="admin@pballstore.com" value="{{ old('email') }}" autocomplete="off">
                    </div>

                    <div class="p-b-20">
                        <label class="stext-102 cl3" for="password">Mật khẩu</label>
                        <div class="pass-group">
                            <input class="size-111 bor8 stext-102 cl2 p-lr-20" type="password" name="password" id="password" placeholder="Nhập mật khẩu" autocomplete="new-password">
                            <i class="fa fa-eye-slash toggle-pass" id="togglePassIcon"></i>
                        </div>
                    </div>

                    <button type="submit" class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer mt-4" style="border-radius: 3px;">
                        ĐĂNG NHẬP ADMIN
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .js-err-msg { color: #dc3545; font-size: 13px; margin-top: 5px; display: block; }
    .bor-red { border: 1px solid #dc3545 !important; }

    .pass-group { position: relative; width: 100%; }
    .pass-group input { padding-right: 45px !important; }
    .toggle-pass { 
        position: absolute; 
        right: 15px; 
        top: 50%; 
        transform: translateY(-50%); 
        cursor: pointer; 
        color: #888; 
        font-size: 18px; 
        z-index: 10;
        transition: color 0.2s;
    }
    .toggle-pass:hover { color: #333; }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        
        $('#togglePassIcon').click(function() {
            let passInput = $('#password');
            let icon = $(this);
            
            if (passInput.attr('type') === 'password') {
                passInput.attr('type', 'text'); 
                icon.removeClass('fa-eye-slash').addClass('fa-eye'); 
            } else {
                passInput.attr('type', 'password'); 
                icon.removeClass('fa-eye').addClass('fa-eye-slash'); 
            }
        });

        $(document).on('submit', '#login-form', function(e) {
            e.preventDefault(); 

            let isValid = true;
            $('.js-err-msg, .backend-err').remove(); 
            $('input').removeClass('bor-red');

            let email = $('input[name="email"]');
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if(!email.val().trim()) {
                showError(email, 'Vui lòng nhập email quản trị.'); isValid = false;
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
            if($(this).attr('id') === 'password') {
                $(this).closest('.p-b-20').find('.js-err-msg, .backend-err').remove();
            } else {
                $(this).parent().find('.js-err-msg, .backend-err').remove();
            }
        });

        function showError(input, message) {
            input.addClass('bor-red');
            if(input.attr('id') === 'password') {
                input.closest('.pass-group').after('<span class="js-err-msg">' + message + '</span>');
            } else {
                input.after('<span class="js-err-msg">' + message + '</span>');
            }
        }
    });
</script>
@endpush
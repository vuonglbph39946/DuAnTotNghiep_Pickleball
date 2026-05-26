@extends('client.layouts.app')

@section('title', 'Liên hệ - PBALL STORE')

@section('content')
<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-color: #1e293b; padding: 60px 0;">
    <h2 class="ltext-105 cl0 txt-center" style="color: white; text-transform: uppercase; font-weight: 700; letter-spacing: 2px;">
        Liên hệ với chúng tôi
    </h2>
</section>

<section class="bg0 p-t-104 p-b-116">
    <div class="container">
        <div class="flex-w flex-tr">
            <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                <form id="fakeContactForm">
                    <h4 class="mtext-105 cl2 txt-center p-b-30" style="font-weight: 600;">
                        Gửi lời nhắn cho shop
                    </h4>

                    <div class="bor8 m-b-20 how-pos4-parent" style="position: relative;">
                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="email" id="contactEmail" placeholder="Địa chỉ Email của bạn" required style="width: 100%; height: 50px; padding-left: 62px; border: none; outline: none;">
                        <i class="fa fa-envelope how-pos4 pointer-none" style="font-size: 18px; color: #888; position: absolute; left: 24px; top: 50%; transform: translateY(-50%);"></i>
                    </div>

                    <div class="bor8 m-b-30">
                        <textarea class="stext-111 cl2 plh3 size-120 p-lr-28 p-tb-25" id="contactMsg" placeholder="Bạn cần chúng tôi hỗ trợ vấn đề gì?" required style="min-height: 150px; width: 100%; padding: 15px; border: none; outline: none;"></textarea>
                    </div>

                    <button type="submit" class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" style="font-weight: bold; background-color: #dc3545; width: 100%; height: 50px; border-radius: 4px; border: none; color: white;">
                        Gửi lời nhắn
                    </button>
                </form>
            </div>

            
        </div>

        <div style="width: 100%; height: 450px; margin-top: 50px; border-radius: 12px; overflow: hidden; border: 1px solid #e6e6e6; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
            <iframe src="https://maps.google.com/maps?q=21.0664,105.7795&z=16&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Luồng xử lý gửi form liên hệ giả lập mượt mà cho Demo
        $('#fakeContactForm').on('submit', function(e) {
            e.preventDefault(); 
            
            var email = $('#contactEmail').val();
            var msg = $('#contactMsg').val();

            if(email && msg) {
                var btn = $(this).find('button[type="submit"]');
                var originalText = btn.text();
                btn.text('Đang gửi lời nhắn...').prop('disabled', true).css('opacity', '0.7');

                setTimeout(function() {
                    // Bắn thông báo SweetAlert thành công bắt mắt
                    swal({
                        title: "Đã gửi thành công!",
                        text: "Cảm ơn bạn đã liên hệ. PBALL STORE sẽ phản hồi lại thông tin qua email của bạn trong thời gian sớm nhất!",
                        icon: "success",
                        button: "Xác nhận",
                    });
                    
                    $('#fakeContactForm')[0].reset();
                    btn.text(originalText).prop('disabled', false).css('opacity', '1');
                }, 1000); 
            }
        });
    });
</script>
@endpush
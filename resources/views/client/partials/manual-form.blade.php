<div class="row">
    <div class="col-sm-6 p-b-15">
        <label class="stext-102 cl3">Họ và tên người nhận <span class="text-danger">*</span></label>
        <input class="size-111 bor8 stext-102 cl2 p-lr-20 @error('customer_name') bor-red @enderror" type="text" name="customer_name" value="{{ old('customer_name') }}" required>
        @error('customer_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    
    <div class="col-sm-6 p-b-15">
        <label class="stext-102 cl3">Số điện thoại <span class="text-danger">*</span></label>
        <input class="size-111 bor8 stext-102 cl2 p-lr-20 @error('customer_phone') bor-red @enderror" type="text" name="customer_phone" value="{{ old('customer_phone') }}" pattern="(84|0[3|5|7|8|9])+([0-9]{8})" title="Vui lòng nhập đúng định dạng số điện thoại (Ví dụ: 0987654321)" required>
        @error('customer_phone') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    
    <div class="col-sm-12 p-b-15">
        <label class="stext-102 cl3">Email liên hệ <span class="text-danger">*</span></label>
        <input class="size-111 bor8 stext-102 cl2 p-lr-20 @error('email') bor-red @enderror" type="email" name="email" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4 p-b-15 select2-wrapper">
        <label class="stext-102 cl3">Tỉnh / Thành phố <span class="text-danger">*</span></label>
        <select class="size-111 bor8 stext-102 cl2 p-lr-20 w-full api_province js-select2" name="province_id">
            <option value="">Tỉnh / Thành phố</option>
        </select>
        <input type="hidden" class="province_name" name="province_name" value="{{ old('province_name') }}">
        @error('province_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4 p-b-15 select2-wrapper">
        <label class="stext-102 cl3">Quận / Huyện <span class="text-danger">*</span></label>
        <select class="size-111 bor8 stext-102 cl2 p-lr-20 w-full api_district js-select2" name="district_id" disabled>
            <option value="">Quận / Huyện</option>
        </select>
        <input type="hidden" class="district_name" name="district_name" value="{{ old('district_name') }}">
    </div>

    <div class="col-md-4 p-b-15 select2-wrapper">
        <label class="stext-102 cl3">Phường / Xã <span class="text-danger">*</span></label>
        <select class="size-111 bor8 stext-102 cl2 p-lr-20 w-full api_ward js-select2" name="ward_id" disabled>
            <option value="">Phường / Xã</option>
        </select>
        <input type="hidden" class="ward_name" name="ward_name" value="{{ old('ward_name') }}">
    </div>

    <div class="col-12 p-b-15">
        <label class="stext-102 cl3">Địa cụ thể (Tên đường, số nhà) <span class="text-danger">*</span></label>
        <input class="size-111 bor8 stext-102 cl2 p-lr-20 @error('specific_address') bor-red @enderror" type="text" name="specific_address" value="{{ old('specific_address') }}" placeholder="VD: Ngõ 3, Phố Trịnh Văn Bô..." required>
        @error('specific_address') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    // 1. TRANG DANH SÁCH MÃ GIẢM GIÁ
    public function index(Request $request)
    {
        $query = Coupon::latest();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $coupons = $query->paginate(15)->withQueryString();
        return view('admin.coupons.index', compact('coupons'));
    }

    // 2. TRANG HIỂN THỊ FORM TẠO MỚI
    public function create()
    {
        return view('admin.coupons.create');
    }

    // 3. XỬ LÝ LƯU VÀO DATABASE
    public function store(Request $request)
    {
        $messages = [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại trong hệ thống.',
            'code.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            'discount_value.required' => 'Vui lòng nhập mức giảm.',
            'discount_value.numeric' => 'Mức giảm phải là chữ số.',
            'discount_value.min' => 'Mức giảm tối thiểu là 5% (nếu chọn phần trăm) hoặc 10.000đ (nếu chọn tiền mặt).',
            'discount_value.max' => 'Mức giảm phần trăm không được vượt quá 100%.',
            'max_discount_value.min' => 'Giảm tối đa không được là số âm.',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'min_order_value.min' => 'Giá trị đơn tối thiểu không được là số âm.',
            'quantity.required' => 'Vui lòng nhập tổng số lượng phát hành.',
            'quantity.min' => 'Số lượng phát hành ít nhất phải là 1.',
            'max_usage_per_user.required' => 'Vui lòng nhập số lần sử dụng cho 1 khách hàng.',
            'max_usage_per_user.min' => 'Số lần sử dụng ít nhất phải là 1.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'start_date.date' => 'Ngày bắt đầu không đúng định dạng.',
            'end_date.date' => 'Ngày kết thúc không đúng định dạng.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải diễn ra sau hoặc bằng ngày bắt đầu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ];

        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'discount_type' => 'required|in:percent,fixed',
            
            // === VÁ LỖI 3 & 6: Điều kiện Động (Dynamic Validation) ===
            // Nếu là phần trăm: Bắt buộc từ 5% đến 100%
            // Nếu là tiền mặt: Bắt buộc từ 10.000đ trở lên
            'discount_value' => 'required|numeric|' . ($request->discount_type == 'percent' ? 'min:5|max:100' : 'min:10000'),
            
            'max_discount_value' => 'nullable|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'max_usage_per_user' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|boolean',
        ], $messages);

        // Ép mã code tự động in hoa
        $data = $request->all();
        $data['code'] = strtoupper($request->code);

        // === ĐÃ FIX: Tự động dọn rác dữ liệu max_discount_value nếu chọn Giảm thẳng ===
        if ($data['discount_type'] === 'fixed') {
            $data['max_discount_value'] = null;
        }

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Thêm mã giảm giá thành công!');
    }

    // 4. TRANG HIỂN THỊ FORM CHỈNH SỬA
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    // 5. XỬ LÝ CẬP NHẬT DATABASE
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $messages = [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại trong hệ thống.',
            'code.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            'discount_value.required' => 'Vui lòng nhập mức giảm.',
            'discount_value.numeric' => 'Mức giảm phải là chữ số.',
            'discount_value.min' => 'Mức giảm tối thiểu là 5% (nếu chọn phần trăm) hoặc 10.000đ (nếu chọn tiền mặt).',
            'discount_value.max' => 'Mức giảm phần trăm không được vượt quá 100%.',
            'max_discount_value.min' => 'Giảm tối đa không được là số âm.',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'min_order_value.min' => 'Giá trị đơn tối thiểu không được là số âm.',
            'quantity.required' => 'Vui lòng nhập tổng số lượng phát hành.',
            'quantity.min' => 'Số lượng phát hành không được là số âm.',
            'max_usage_per_user.required' => 'Vui lòng nhập số lần sử dụng cho 1 khách hàng.',
            'max_usage_per_user.min' => 'Số lần sử dụng ít nhất phải là 1.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'start_date.date' => 'Ngày bắt đầu không đúng định dạng.',
            'end_date.date' => 'Ngày kết thúc không đúng định dạng.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải diễn ra sau hoặc bằng ngày bắt đầu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ];

        $request->validate([
            // Bỏ qua check unique cho chính nó đang sửa
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount_type' => 'required|in:percent,fixed',
            
            // === VÁ LỖI 3 & 6: Tương tự như trên hàm store ===
            'discount_value' => 'required|numeric|' . ($request->discount_type == 'percent' ? 'min:5|max:100' : 'min:10000'),
            
            'max_discount_value' => 'nullable|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'max_usage_per_user' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|boolean',
        ], $messages);

        $data = $request->all();
        $data['code'] = strtoupper($request->code);

        // === ĐÃ FIX: Tự động dọn rác dữ liệu max_discount_value nếu chọn Giảm thẳng ===
        if ($data['discount_type'] === 'fixed') {
            $data['max_discount_value'] = null;
        }

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    // 6. XỬ LÝ XÓA MÃ GIẢM GIÁ
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return back()->with('success', 'Đã xóa mã giảm giá!');
    }
}
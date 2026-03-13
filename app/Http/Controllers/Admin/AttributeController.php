<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    private function getAdminMock()
    {
        return (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
    }

    public function index()
    {
        $admin = $this->getAdminMock();
        $attributes = Attribute::with('values')->latest()->paginate(10);
        return view('admin.attributes.index', compact('admin', 'attributes'));
    }

    public function create()
    {
        $admin = $this->getAdminMock();
        return view('admin.attributes.create', compact('admin'));
    }

    public function store(Request $request)
    {
        // Chỉ validate tên nhóm, vì phần values giờ là mảng động từ JS
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
        ]);

        $attribute = Attribute::create(['name' => $request->name]);

        // Lưu danh sách giá trị (mảng name="values[0][value]")
        if ($request->has('values') && is_array($request->values)) {
            foreach ($request->values as $valData) {
                // Chỉ lưu nếu admin có nhập tên (VD: Đỏ, XL)
                if (!empty(trim($valData['value']))) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => trim($valData['value']),
                        'color_code' => $valData['color_code'] ?? null // Lưu màu nếu có
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.attributes.index')->with('success', 'Đã thêm nhóm thuộc tính thành công!');
    }

    public function edit($id)
    {
        $admin = $this->getAdminMock();
        $attribute = Attribute::with('values')->findOrFail($id);
        
        return view('admin.attributes.edit', compact('admin', 'attribute'));
    }

    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $id,
        ]);

        $attribute->update(['name' => $request->name]);

        $existingIds = $attribute->values->pluck('id')->toArray();
        $submittedIds = [];

        // Cập nhật danh sách giá trị động
        if ($request->has('values') && is_array($request->values)) {
            foreach ($request->values as $valData) {
                if (empty(trim($valData['value']))) continue;

                if (isset($valData['id']) && !empty($valData['id'])) {
                    // Update giá trị ĐÃ CÓ trong database
                    $submittedIds[] = $valData['id'];
                    $attrVal = AttributeValue::find($valData['id']);
                    if ($attrVal) {
                        $attrVal->update([
                            'value' => trim($valData['value']),
                            'color_code' => $valData['color_code'] ?? null
                        ]);
                    }
                } else {
                    // Thêm giá trị MỚI vừa bấm "Thêm dòng"
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => trim($valData['value']),
                        'color_code' => $valData['color_code'] ?? null
                    ]);
                }
            }
        }

        // Xóa những dòng bị Admin bấm icon Thùng Rác (Xóa khỏi DB nếu chưa xài ở sản phẩm nào)
        $idsToRemove = array_diff($existingIds, $submittedIds);
        foreach ($idsToRemove as $idToRemove) {
            $attrVal = AttributeValue::find($idToRemove);
            if ($attrVal && $attrVal->variants()->count() == 0) {
                $attrVal->delete();
            }
        }
        
        return redirect()->route('admin.attributes.index')->with('success', 'Đã cập nhật thuộc tính thành công!');
    }

    public function destroy($id)
    {
        $attribute = Attribute::with('values.variants')->findOrFail($id);
        
        foreach ($attribute->values as $val) {
            if ($val->variants()->count() > 0) {
                return back()->with('error', 'Không thể xóa! Đang có sản phẩm sử dụng thuộc tính này.');
            }
        }
        
        $attribute->delete();
        return back()->with('success', 'Xóa nhóm thuộc tính thành công!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoryController extends Controller
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
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('admin', 'categories'));
    }

    public function create()
    {
        $admin = $this->getAdminMock();
        return view('admin.categories.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:0,1'
        ]);

        $data = $request->except(['image']);
        
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function show($id)
    {
        $admin = $this->getAdminMock();
        $category = Category::with('products')->findOrFail($id);
        return view('admin.categories.show', compact('admin', 'category'));
    }

    public function edit($id)
    {
        $admin = $this->getAdminMock();
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('admin', 'category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:0,1'
        ]);

        // Lấy dữ liệu ngoại trừ image và remove_image để tự xử lý riêng
        $data = $request->except(['image', 'remove_image']);
        $data['slug'] = Str::slug($request->name);

        // BẮT ĐẦU XỬ LÝ ẢNH
        // 1. Nếu anh bấm dấu X (remove_image = 1)
        if ($request->remove_image == '1') {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $data['image'] = null; // Cập nhật DB thành rỗng
        } 
        // 2. Nếu anh tải ảnh mới lên
        elseif ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            // Lưu ảnh mới
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }
        // KẾT THÚC XỬ LÝ ẢNH

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa vì đang có sản phẩm thuộc danh mục này.');
        }

        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();

        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
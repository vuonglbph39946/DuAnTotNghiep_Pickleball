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
        $totalCategories = Category::count(); // Đếm tổng số danh mục
        // Chỉ lấy danh mục GỐC (parent_id = null) và kèm theo các danh mục CON của nó
        $categories = Category::whereNull('parent_id')->with('children')->latest()->get();
        
        return view('admin.categories.index', compact('admin', 'categories', 'totalCategories'));
    }

    public function create()
    {
        $admin = $this->getAdminMock();
        // Lấy các danh mục GỐC để người dùng chọn làm Cha
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('admin', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories|max:255',
            'parent_id' => 'nullable|exists:categories,id', // THÊM VALIDATE PARENT_ID
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
        // Lấy danh mục GỐC, nhưng PHẢI LOẠI TRỪ chính nó (Không thể tự nhận mình làm cha)
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        
        return view('admin.categories.edit', compact('admin', 'category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $id,
            'parent_id' => 'nullable|exists:categories,id', // THÊM VALIDATE PARENT_ID
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:0,1'
        ]);

        $data = $request->except(['image', 'remove_image']);
        $data['slug'] = Str::slug($request->name);

        // Xử lý ảnh (giữ nguyên logic cũ cực xịn của anh em mình)
        if ($request->remove_image == '1') {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        $category = Category::with('children')->findOrFail($id);

        // Chặn xóa nếu có sản phẩm
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa vì đang có sản phẩm thuộc danh mục này.');
        }
        
        // Chặn xóa nếu danh mục này đang là CHA của các danh mục khác
        if ($category->children->count() > 0) {
            return back()->with('error', 'Không thể xóa vì danh mục này đang chứa các danh mục con. Hãy xóa danh mục con trước!');
        }

        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();

        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
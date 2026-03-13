<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    private function getAdminMock()
    {
        return (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
    }

    public function index()
    {
        $admin = $this->getAdminMock();
        // Sắp xếp theo vị trí (position) tăng dần, rồi đến ID mới nhất
        $banners = Banner::orderBy('position', 'asc')->orderBy('id', 'desc')->paginate(10);
        
        return view('admin.banners.index', compact('admin', 'banners'));
    }

    public function create()
    {
        $admin = $this->getAdminMock();
        return view('admin.banners.create', compact('admin'));
    }

   public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'link' => 'nullable|url|max:255', // Đã đổi thành URL để kiểm tra link chuẩn
            'position' => 'nullable|integer|min:0',
        ], [
            'image.required' => 'Vui lòng chọn hình ảnh Banner tải lên!',
            'image.image' => 'File tải lên bắt buộc phải là hình ảnh.',
            'image.mimes' => 'Chỉ chấp nhận ảnh định dạng: jpeg, png, jpg, webp.',
            'image.max' => 'Dung lượng ảnh không được vượt quá 3MB.',
            'link.url' => 'Đường dẫn (Link) không hợp lệ (Phải bắt đầu bằng http:// hoặc https://).',
            'position.integer' => 'Vị trí phải là một số nguyên.'
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $imageName = 'banner_' . time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $imagePath = 'uploads/banners/' . $imageName;
        }

        Banner::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'link' => $request->link,
            'position' => $request->position ?? 0,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm Banner thành công!');
    }

    public function edit($id)
    {
        $admin = $this->getAdminMock();
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('admin', 'banner'));
    }

   public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'link' => 'nullable|url|max:255',
            'position' => 'nullable|integer|min:0',
        ], [
            'image.image' => 'File tải lên bắt buộc phải là hình ảnh.',
            'image.mimes' => 'Chỉ chấp nhận ảnh định dạng: jpeg, png, jpg, webp.',
            'image.max' => 'Dung lượng ảnh không được vượt quá 3MB.',
            'link.url' => 'Đường dẫn (Link) không hợp lệ (Phải bắt đầu bằng http:// hoặc https://).',
            'position.integer' => 'Vị trí phải là một số nguyên.'
        ]);

        $data = [
            'title' => $request->title,
            'link' => $request->link,
            'position' => $request->position ?? 0,
            'status' => $request->status ?? 1,
        ];

        if ($request->hasFile('image')) {
            if (File::exists(public_path($banner->image_path))) {
                File::delete(public_path($banner->image_path));
            }
            
            $imageName = 'banner_' . time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $data['image_path'] = 'uploads/banners/' . $imageName;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật Banner thành công!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Xóa ảnh vật lý trong folder
        if (File::exists(public_path($banner->image_path))) {
            File::delete(public_path($banner->image_path));
        }
        
        $banner->delete();

        return back()->with('success', 'Đã xóa Banner khỏi hệ thống!');
    }
}
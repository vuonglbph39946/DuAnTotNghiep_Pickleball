<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'images', 'reply'])->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1,2'
        ]);

        $review = Review::findOrFail($id);
        $review->update(['status' => $request->status]);

        $statusText = $request->status == 1 ? 'duyệt hiển thị' : 'ẩn';
        return back()->with('success', "Đã {$statusText} đánh giá này thành công!");
    }

    public function destroy($id)
    {
        $review = Review::with('images')->findOrFail($id);

        // FIX LỖI 3: Dùng đường dẫn sạch truyền thẳng vào Storage
        if ($review->images->count() > 0) {
            foreach ($review->images as $img) {
                Storage::disk('public')->delete($img->image_path);
            }
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá và dọn dẹp hình ảnh thành công!');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ], [
            'content.required' => 'Vui lòng nhập nội dung phản hồi.'
        ]);

        ReviewReply::updateOrCreate(
            ['review_id' => $id],
            [
                'admin_id' => Auth::id(),
                'content' => $request->content
            ]
        );

        return back()->with('success', 'Đã lưu phản hồi thành công!');
    }
}
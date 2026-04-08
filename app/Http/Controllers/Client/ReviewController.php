<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
use App\Models\ReviewImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image; // THƯ VIỆN NÉN ẢNH

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        if (!Auth::check()) {
            return back()->with('error', 'Vui lòng đăng nhập để thực hiện chức năng này.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'images' => 'nullable|array|max:5',
            // Nâng max lên 5MB để khách up thoải mái, mình sẽ nén lại sau
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120' 
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'comment.required' => 'Vui lòng nhập nội dung đánh giá của bạn.',
            'images.max' => 'Bạn chỉ được tải lên tối đa 5 hình ảnh.',
            'images.*.image' => 'File tải lên không hợp lệ, vui lòng chọn file hình ảnh.',
            'images.*.max' => 'Kích thước mỗi ảnh không được vượt quá 5MB.'
        ]);

        $userId = Auth::id();

        $eligibleOrder = Order::where('user_id', $userId)
            ->where('order_status', 'completed')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->whereNotIn('id', function($query) use ($userId, $productId) {
                $query->select('order_id')
                      ->from('reviews')
                      ->where('user_id', $userId)
                      ->where('product_id', $productId);
            })
            ->first();

        if (!$eligibleOrder) {
            return back()->with('error', 'Bạn chưa mua sản phẩm này, hoặc đã đánh giá hết lượt!');
        }

        try {
            DB::beginTransaction();

            $review = Review::firstOrCreate([
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $eligibleOrder->id,
            ], [
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 0 
            ]);

            // ========================================================
            // ĐÃ NÂNG CẤP: NÉN ẢNH VÀ LƯU PATH SẠCH
            // ========================================================
            if ($review->wasRecentlyCreated && $request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    
                    // Đưa hết về đuôi .jpg cho nhẹ và dễ quản lý
                    $filename = time() . '_' . Str::random(5) . '.jpg';
                    $path = 'reviews/' . $filename;

                    // Resize ảnh xuống còn max width 800px, giữ tỉ lệ, nén 80% chất lượng
                    $image = Image::make($file)->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize(); // Không làm mờ nếu ảnh gốc đã nhỏ sẵn
                    })->encode('jpg', 80);

                    // Lưu vào Storage an toàn (Lưu file vật lý)
                    Storage::disk('public')->put($path, $image);
                    
                    ReviewImage::create([
                        'review_id' => $review->id,
                        'image_path' => $path // LƯU ĐƯỜNG DẪN SẠCH VÀO DB (KHÔNG CÓ CHỮ storage/)
                    ]);
                }
            }

            DB::commit(); 
            return back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ Admin kiểm duyệt!');
            
        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
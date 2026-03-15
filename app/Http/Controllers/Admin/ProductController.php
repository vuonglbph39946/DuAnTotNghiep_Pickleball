<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Attribute;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function getAdminMock()
    {
        return (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
    }

    public function index(Request $request)
    {
        $admin = $this->getAdminMock();
        
        // 1. Eager Loading để tránh lag web
        $query = Product::with(['category', 'images', 'variants.attributeValues'])->latest();

        // 2. Bộ lọc theo Danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Bộ lọc theo Trạng thái (Đang bán / Ẩn)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Tìm kiếm thông minh: Tìm theo Tên SP, Mã SKU gốc, hoặc Mã SKU của biến thể
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('variants', function($vq) use ($search) {
                      $vq->where('sku', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->get(); // Lấy danh mục để ra View làm bộ lọc

        return view('admin.products.index', compact('admin', 'products', 'categories'));
    }

    public function create()
    {
        $admin = $this->getAdminMock();
        $categories = Category::whereNull('parent_id')->orWhere('parent_id', 0)
                              ->with(['children' => function($q) { $q->where('status', 1); }])
                              ->where('status', 1)->get();
        $attributes = Attribute::with('values')->get(); 
        return view('admin.products.create', compact('admin', 'categories', 'attributes'));
    }

    public function store(Request $request)
    {
        // ĐÃ VÁ LỖ HỔNG VALIDATE: Bổ sung check mảng biến thể, chống hacker chèn ID lạ
        $request->validate([
            'name' => 'required|max:255|unique:products',
            'sku' => 'nullable|string|max:100|unique:products', 
            'category_id' => 'required|exists:categories,id',
            'price' => 'required_without:has_variants|numeric|min:0|nullable',
            'stock' => 'required_without:has_variants|integer|min:0|nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants.*.price' => 'required_with:has_variants|numeric|min:0',
            'variants.*.stock' => 'required_with:has_variants|integer|min:0',
            // BẢO VỆ CHUẨN XÁC: Đảm bảo ID biến thể truyền lên là hợp lệ
            'variants.*.attribute_values' => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::beginTransaction();
        try {
            $basePrice = (float)($request->price ?? 0);
            $baseStock = (int)($request->stock ?? 0);
            $baseSalePrice = $request->sale_price ? (float)$request->sale_price : null;

            $variantsData = $request->input('variants', []);
            $isVariantActive = $request->boolean('has_variants') && count($variantsData) > 0;

            if ($isVariantActive) {
                $baseStock = collect($variantsData)->sum('stock');
                $basePrice = collect($variantsData)->min('price');
            }

            // 1. Tạo sản phẩm Gốc
            $product = Product::create([
                'name'        => $request->name,
                'slug'        => Str::slug($request->name),
                'sku'         => $request->sku, // Lưu Mã SP gốc
                'category_id' => $request->category_id,
                'description' => $request->description,
                'status'      => $request->status ?? 1,
                'price'       => $basePrice,
                'sale_price'  => $baseSalePrice,
                'stock'       => $baseStock,
            ]);

            // 2. Lưu Hình ảnh
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('uploads/products'), $imageName);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => 'uploads/products/' . $imageName
                    ]);
                }
            }

            // 3. Lưu Biến Thể
            if ($isVariantActive) {
                foreach ($variantsData as $varData) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku'        => $varData['sku'] ?? null,
                        'price'      => $varData['price'] ?? 0,
                        'sale_price' => $varData['sale_price'] ?? null,
                        'stock'      => $varData['stock'] ?? 0,
                    ]);

                    if (isset($varData['attribute_values'])) {
                        $variant->attributeValues()->attach($varData['attribute_values']);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $admin = $this->getAdminMock();
        $product = Product::with(['category', 'images', 'variants.attributeValues'])->findOrFail($id);

        $analytics = (object)[
            'total_sold' => 0,
            'revenue'    => 0,
            'views'      => rand(150, 999) 
        ];

        return view('admin.products.show', compact('admin', 'product', 'analytics'));
    }

   public function edit($id)
    {
        $admin = $this->getAdminMock();
        $product = Product::with(['images', 'variants.attributeValues'])->findOrFail($id);
        
        $categories = Category::whereNull('parent_id')->orWhere('parent_id', 0)
                              ->with(['children' => function($q) { $q->where('status', 1); }])
                              ->where('status', 1)->get();
                              
        $attributes = Attribute::with('values')->get(); 
        
        return view('admin.products.edit', compact('admin', 'product', 'categories', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // ĐÃ VÁ LỖ HỔNG VALIDATE: Cập nhật
        $request->validate([
            'name' => 'required|max:255|unique:products,name,' . $id,
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required_without:has_variants|numeric|min:0|nullable',
            'stock' => 'required_without:has_variants|integer|min:0|nullable',
            'status' => 'required|in:0,1',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants.*.price' => 'required_with:has_variants|numeric|min:0',
            'variants.*.stock' => 'required_with:has_variants|integer|min:0',
            // BẢO VỆ CHUẨN XÁC: Đảm bảo ID biến thể truyền lên là hợp lệ khi sửa
            'variants.*.attribute_values' => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::beginTransaction();
        try {
            $isVariantActive = $request->boolean('has_variants') && $request->has('variants') && count($request->variants) > 0;

            $basePrice = (float)($request->price ?? 0);
            $baseStock = (int)($request->stock ?? 0);
            $baseSalePrice = $request->sale_price ? (float)$request->sale_price : null;

            if ($isVariantActive) {
                $baseStock = collect($request->variants)->sum('stock');
                $basePrice = collect($request->variants)->min('price');
            }

            // 1. Cập nhật Sản phẩm Gốc
            $product->update([
                'name'        => $request->name,
                'slug'        => Str::slug($request->name),
                'sku'         => $request->sku,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'status'      => $request->status ?? 1,
                'price'       => $basePrice,
                'sale_price'  => $baseSalePrice,
                'stock'       => $baseStock,
            ]);

            // 2. Thêm Hình ảnh mới
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('uploads/products'), $imageName);
                    ProductImage::create(['product_id' => $product->id, 'image_path' => 'uploads/products/' . $imageName]);
                }
            }

            // 3. XỬ LÝ BIẾN THỂ (CÓ BẢO VỆ CHỐNG XÓA KHI ĐANG CÓ ĐƠN HÀNG)
            $existingVariantIds = $product->variants->pluck('id')->toArray();

            if ($isVariantActive) {
                $submittedVariantIds = [];

                foreach ($request->variants as $varData) {
                    if (isset($varData['id']) && $varData['id']) {
                        $submittedVariantIds[] = $varData['id'];
                        $variant = ProductVariant::find($varData['id']);
                        if ($variant) {
                            $variant->update([
                                'sku' => $varData['sku'] ?? null,
                                'price' => $varData['price'] ?? 0,
                                'sale_price' => $varData['sale_price'] ?? null,
                                'stock' => $varData['stock'] ?? 0,
                            ]);
                            if (isset($varData['attribute_values'])) {
                                $variant->attributeValues()->sync($varData['attribute_values']);
                            }
                        }
                    } else {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $varData['sku'] ?? null,
                            'price' => $varData['price'] ?? 0,
                            'sale_price' => $varData['sale_price'] ?? null,
                            'stock' => $varData['stock'] ?? 0,
                        ]);
                        if (isset($varData['attribute_values'])) {
                            $variant->attributeValues()->attach($varData['attribute_values']);
                        }
                    }
                }

                // TÌM RA NHỮNG BIẾN THỂ MÀ ADMIN VỪA BẤM NÚT "XÓA"
                $variantsToDelete = array_diff($existingVariantIds, $submittedVariantIds);
                
                if (!empty($variantsToDelete)) {
                    // Kiểm tra xem biến thể đó có đang nằm trong hóa đơn nào không
                    $hasOrders = DB::table('order_items')->whereIn('product_variant_id', $variantsToDelete)->exists();
                    if ($hasOrders) {
                        throw new \Exception('KHÔNG THỂ XÓA! Phân loại bạn vừa xóa đang nằm trong đơn hàng của khách.');
                    }
                    ProductVariant::whereIn('id', $variantsToDelete)->delete();
                }

            } else {
                // NẾU ADMIN TẮT HẲN NÚT "SẢN PHẨM CÓ BIẾN THỂ"
                if (!empty($existingVariantIds)) {
                    $hasOrders = DB::table('order_items')->whereIn('product_variant_id', $existingVariantIds)->exists();
                    if ($hasOrders) {
                        throw new \Exception('KHÔNG THỂ TẮT BIẾN THỂ! Một số phân loại của sản phẩm này đã được khách hàng đặt mua.');
                    }
                    $product->variants()->delete();
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::with(['images', 'orderItems'])->findOrFail($id);
        if ($product->orderItems->count() > 0) return back()->with('error', 'Không thể xóa! Sản phẩm đã phát sinh đơn hàng.');

        foreach ($product->images as $image) {
            if (File::exists(public_path($image->image_path))) File::delete(public_path($image->image_path));
        }
        $product->images()->delete(); 
        $product->delete();
        return back()->with('success', 'Xóa sản phẩm thành công!');
    }
    
    public function destroyImage($imageId)
    {
        $image = ProductImage::findOrFail($imageId);
        if (File::exists(public_path($image->image_path))) File::delete(public_path($image->image_path));
        $image->delete();
        return response()->json(['success' => true]);
    }
}
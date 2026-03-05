<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Danh sách tin tức (phân trang + tìm kiếm)
     */
    public function index(Request $request)
    {
        $query = News::with('category')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('summary', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->paginate(10)->withQueryString();

        return view('admin.news.index', compact('rows', 'search'));
    }

    /**
     * Hiển thị form tạo tin tức mới
     */
    public function create()
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        return view('admin.news.create', compact('categories'));
    }

    /**
     * Tạo tin tức mới (AJAX hoặc form thường)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'author_id' => 'nullable|integer',
            'status' => 'required|integer|in:0,1,2',
            'published_at' => 'nullable|date',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['title']) . '-' . substr(uniqid(), -5);
        }

        $news = News::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'News created successfully!',
                'news' => $news,
            ]);
        }

        return redirect()->route('admin.news.index')->with('success', 'News created successfully!');
    }

    /**
     * Hiển thị chi tiết tin tức
     */
    public function show(News $news)
    {
        $news->load('category');
        return view('admin.news.show', compact('news'));
    }

    /**
     * Hiển thị form chỉnh sửa tin tức
     */
    public function edit(News $news)
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    /**
     * Cập nhật tin tức
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $news->id,
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'author_id' => 'nullable|integer',
            'status' => 'required|integer|in:0,1,2',
            'published_at' => 'nullable|date',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['title']) . '-' . substr(uniqid(), -5);
        }

        $news->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'News updated successfully!',
                'news' => $news->fresh(),
            ]);
        }

        return redirect()->route('admin.news.index')->with('success', 'News updated successfully!');
    }

    /**
     * Cập nhật status của tin tức
     */
    public function updateStatus(Request $request, News $news)
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $news->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'news' => $news->fresh(),
        ]);
    }

    /**
     * Xoá tin tức
     */
    public function destroy(Request $request, News $news)
    {
        $news->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'News deleted successfully!',
            ]);
        }

        return redirect()->route('admin.news.index')->with('success', 'News deleted successfully!');
    }
}


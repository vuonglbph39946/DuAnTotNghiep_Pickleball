<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Danh sách categories (phân trang + tìm kiếm) - Tree structure
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        // Load all categories with parent and children relationships
        $allCategories = Category::with(['parent', 'children'])->orderBy('name')->get();
        
        // Build tree structure
        $treeCategories = $this->buildTree($allCategories, null, $search);
        
        // Paginate only root categories (10 per page)
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        
        // Get only root categories for pagination
        $rootCategories = collect($treeCategories);
        $totalRoots = $rootCategories->count();
        
        // Paginate root categories
        $paginatedRoots = $rootCategories->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        // Flatten tree to list with level information (only for paginated roots and their children)
        $flattenedCategories = [];
        foreach ($paginatedRoots as $rootCategory) {
            $this->flattenTree([$rootCategory], $flattenedCategories, 0, false); // Include all children
        }
        
        // Calculate root index for each category (based on all roots, not just paginated)
        $rootIndex = (($currentPage - 1) * $perPage);
        foreach ($flattenedCategories as $category) {
            if (empty($category->parent_id)) {
                $rootIndex++;
                $category->root_index = $rootIndex;
            } else {
                $category->root_index = null; // Children không có root_index
            }
        }
        
        // Create custom paginator for root categories only
        $rows = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($flattenedCategories),
            $totalRoots, // Total count of root categories only
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.index', compact('rows', 'search', 'parentCategories', 'allCategories', 'flattenedCategories'));
    }

    /**
     * Build tree structure from flat list
     */
    private function buildTree($categories, $parentId = null, $search = null)
    {
        $tree = [];
        
        foreach ($categories as $category) {
            // Filter by search if provided
            if ($search) {
                $matchesSearch = stripos($category->name, $search) !== false 
                    || stripos($category->slug, $search) !== false;
                
                // Include if matches or has matching children
                $hasMatchingChildren = $category->children->filter(function($child) use ($search) {
                    return stripos($child->name, $search) !== false 
                        || stripos($child->slug, $search) !== false;
                })->count() > 0;
                
                if (!$matchesSearch && !$hasMatchingChildren) {
                    continue;
                }
            }
            
            if ($category->parent_id == $parentId) {
                $category->children_list = $this->buildTree($categories, $category->id, $search);
                $tree[] = $category;
            }
        }
        
        return $tree;
    }

    /**
     * Flatten tree to list with level
     */
    private function flattenTree($tree, &$result, $level = 0, $collapsed = false)
    {
        foreach ($tree as $category) {
            $category->level = $level;
            $hasChildren = !empty($category->children_list) || $category->children->count() > 0;
            $category->has_children = $hasChildren;
            $category->is_leaf = !$hasChildren; // Leaf node = không có children
            $result[] = $category;
            
            // Chỉ flatten children nếu không collapsed
            if (!empty($category->children_list) && !$collapsed) {
                $this->flattenTree($category->children_list, $result, $level + 1, $collapsed);
            }
        }
    }

    /**
     * Hiển thị form tạo category mới
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        $mode = 'create';
        $category = null; // No category for create
        return view('admin.categories.form', compact('parentCategories', 'mode', 'category'));
    }

    /**
     * Tạo category mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'status' => 'required|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']) . '-' . substr(uniqid(), -5);
        }

        $category = Category::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => $category->load('parent'),
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Hiển thị chi tiết category hoặc form (create/edit/view)
     */
    public function form(Request $request, Category $category)
    {
        // Get mode from query parameter or default to 'view'
        $mode = $request->get('mode', 'view');
        
        // Validate mode
        if (!in_array($mode, ['create', 'edit', 'view'])) {
            $mode = 'view';
        }

        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        if ($mode === 'view') {
            $category->load(['parent', 'children', 'news']);
        } else {
            // Edit mode - exclude current category from parent list
            $parentCategories = $parentCategories->where('id', '!=', $category->id);
        }

        if ($request->ajax() && $mode === 'view') {
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);
        }

        return view('admin.categories.form', compact('category', 'parentCategories', 'mode'));
    }

    /**
     * Hiển thị chi tiết category (backward compatibility)
     */
    public function show(Request $request, Category $category)
    {
        $category->load('parent');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);
        }
        
        $request->merge(['mode' => 'view']);
        return $this->form($request, $category);
    }

    /**
     * Hiển thị form chỉnh sửa category
     */
    public function edit(Category $category)
    {
        $request = request();
        $request->merge(['mode' => 'edit']);
        return $this->form($request, $category);
    }

    /**
     * Cập nhật category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|integer|exists:categories,id|not_in:' . $category->id,
            'status' => 'required|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']) . '-' . substr(uniqid(), -5);
        }

        $category->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'category' => $category->fresh()->load('parent'),
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Xoá category
     */
    public function destroy(Request $request, Category $category)
    {
        // Kiểm tra nếu có children, không cho xóa
        if ($category->children()->count() > 0) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with subcategories. Please delete subcategories first.',
                ], 400);
            }
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with subcategories. Please delete subcategories first.');
        }

        $category->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!',
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}

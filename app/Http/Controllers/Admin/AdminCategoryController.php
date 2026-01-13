<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = Category::query();

        if ($request->filled('keyword')) {
            $q->where(function ($qq) use ($request) {
                $qq->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('slug', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('status')) {
            $q->where('is_active', $request->status === 'active');
        }

        if ($request->filled('parent')) {
            $q->where('parent_id', $request->parent);
        }

        $parents = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $q->withCount('products')
            ->withCount('children')
            ->with('parent');

        $categories = $q->orderByRaw('parent_id is not null') // parent first
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();


        return view('admin.categories.index', compact('categories', 'parents'));
    }

    public function create()
    {
        $category = new Category();

        // ✅ 只给 parent 列表（parent_id = null）
        $parents = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.form', compact('category', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', Rule::unique('categories', 'slug')],
            'parent_id' => ['nullable', 'exists:categories,id'], // ✅ add
            'icon' => ['nullable', 'image', 'max:1024'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        // ✅ 如果是 sub category，通常 icon 可以不需要（你要保留也可以）
        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        // ✅ parent 下拉：不能选自己 + 只能选 parent
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.form', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', Rule::unique('categories', 'slug')->ignore($category->id)],
            'parent_id' => ['nullable', 'exists:categories,id'], // ✅ add
            'icon' => ['nullable', 'image', 'max:1024'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // ✅ 防止把自己设成自己的 parent
        if (!empty($data['parent_id']) && (int)$data['parent_id'] === (int)$category->id) {
            return back()->withErrors('Parent category cannot be itself.');
        }

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        // ✅ 有子分类不能删（避免 orphan）
        if ($category->children()->exists()) {
            return back()->withErrors('This category has sub-categories. Please move/delete them first.');
        }

        if ($category->products()->exists()) {
            return back()->withErrors('This category has products. Please move or delete them first.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}

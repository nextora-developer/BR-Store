<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopupBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPopupBannerController extends Controller
{
    public function index()
    {
        $banners = PopupBanner::latest()->paginate(15);
        return view('admin.popup-banners.index', compact('banners'));
    }

    public function create()
    {
        $banner = new PopupBanner();
        return view('admin.popup-banners.form', compact('banner'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
            'link' => ['nullable', 'string', 'max:255'],
            'cooldown_days' => ['required', 'integer', 'min:0', 'max:3650'],
            'is_active' => ['nullable'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $path = $request->file('image')->store('popup-banners', 'public');
        $data['image'] = $path;

        $banner = PopupBanner::create($data);

        // ✅ 保证只有一个 active
        if ($banner->is_active) {
            PopupBanner::where('id', '!=', $banner->id)->update(['is_active' => false]);
        }

        return redirect()->route('admin.popup-banners.index')->with('success', 'Banner created.');
    }

    public function edit(PopupBanner $popup_banner)
    {
        $banner = $popup_banner;
        return view('admin.popup-banners.form', compact('banner'));
    }

    public function update(Request $request, PopupBanner $popup_banner)
    {
        $banner = $popup_banner;

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
            'link' => ['nullable', 'string', 'max:255'],
            'cooldown_days' => ['required', 'integer', 'min:0', 'max:3650'],
            'is_active' => ['nullable'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        // 换图
        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('popup-banners', 'public');
        }

        $banner->update($data);

        // ✅ 保证只有一个 active
        if ($banner->is_active) {
            PopupBanner::where('id', '!=', $banner->id)->update(['is_active' => false]);
        }

        return redirect()->route('admin.popup-banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(PopupBanner $popup_banner)
    {
        $banner = $popup_banner;

        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return back()->with('success', 'Banner deleted.');
    }

    // ✅ 快速切换 active/inactive（并且只允许一个 active）
    public function toggle(PopupBanner $popup_banner)
    {
        $banner = $popup_banner;

        $new = !$banner->is_active;
        $banner->update(['is_active' => $new]);

        if ($new) {
            PopupBanner::where('id', '!=', $banner->id)->update(['is_active' => false]);
        }

        return back()->with('success', 'Status updated.');
    }
}

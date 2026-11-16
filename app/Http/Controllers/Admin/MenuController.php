<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category_id', '');

        $menus = Menu::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($categoryId !== '', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.menus.index', compact('menus', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.menus.create', compact('categories'));
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($data);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);

        if ($request->boolean('remove_image') && $menu->image_path) {
            Storage::disk('public')->delete($menu->image_path);
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($menu->image_path) {
                Storage::disk('public')->delete($menu->image_path);
            }

            $data['image_path'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        try {
            $imagePath = $menu->image_path;
            $menu->delete();

            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return redirect()
                ->route('admin.menus.index')
                ->with('success', 'Menu berhasil dihapus.');
        } catch (QueryException) {
            return back()->withErrors([
                'delete' => 'Menu tidak dapat dihapus karena sudah terhubung dengan data transaksi.',
            ]);
        }
    }
}
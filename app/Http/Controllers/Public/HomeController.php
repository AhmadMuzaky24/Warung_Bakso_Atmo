<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $hide = config('bakso_atmo.hide_unavailable');

        $categories = Category::query()
            ->with(['menus' => function ($query) use ($hide) {
                $query->when($hide, fn ($q) => $q->where('is_available', true))
                      ->orderBy('name');
            }])
            ->orderBy('name')
            ->get()
            ->filter(fn ($category) => $category->menus->isNotEmpty());

        return view('public.home', compact('categories'));
    }
}
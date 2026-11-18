<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class CatalogController extends Controller
{
    public function show(Menu $menu)
    {
        if (config('bakso_atmo.hide_unavailable') && ! $menu->is_available) {
            abort(404);
        }

        $menu->load('category');

        return view('public.menus.show', compact('menu'));
    }
}
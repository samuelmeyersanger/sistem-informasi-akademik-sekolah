<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        // Cari halaman dinamis yang berstatus published berdasarkan slug
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return view('publik.page.show', compact('page'));
    }
}
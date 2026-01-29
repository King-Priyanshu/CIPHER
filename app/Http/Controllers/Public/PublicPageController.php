<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    /**
     * Show a dynamic content page by slug.
     */
    public function show($slug)
    {
        $page = ContentPage::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('public.page', compact('page'));
    }
}

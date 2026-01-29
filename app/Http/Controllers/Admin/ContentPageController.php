<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentPageController extends Controller
{
    public function index()
    {
        $pages = ContentPage::latest()->get();
        return view('admin.content.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.content.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        ContentPage::create($validated);

        return redirect()->route('admin.content.index')->with('success', 'Page created successfully.');
    }

    public function edit(ContentPage $page)
    {
        return view('admin.content.edit', compact('page'));
    }

    public function update(Request $request, ContentPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $page->update($validated);

        return redirect()->route('admin.content.index')->with('success', 'Page updated successfully.');
    }
}

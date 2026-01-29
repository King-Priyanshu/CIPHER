@extends('components.layouts.admin')

@section('page_title', 'Edit Page')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="card">
            <h3 class="text-xl font-bold text-navy mb-6">Edit Page: {{ $page->title }}</h3>
            
            <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-navy mb-1.5">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required
                            class="input-field">
                        @error('title')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-navy mb-1.5">Meta Title (SEO)</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                            class="input-field">
                        @error('meta_title')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="meta_description" class="block text-sm font-medium text-navy mb-1.5">Meta Description (SEO)</label>
                        <input type="text" name="meta_description" id="meta_description" value="{{ old('meta_description', $page->meta_description) }}"
                            class="input-field">
                        @error('meta_description')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center md:col-span-2">
                        <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-teal focus:ring-teal">
                        <label for="is_published" class="ml-2 block text-sm text-navy">Published</label>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="content" class="block text-sm font-medium text-navy mb-1.5">Content (HTML)</label>
                    <textarea name="content" id="content" rows="12" required
                        class="input-field font-mono text-sm">{{ old('content', $page->content) }}</textarea>
                    <p class="mt-1 text-xs text-slate">Basic HTML is supported.</p>
                    @error('content')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-end space-x-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.pages.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update Page</button>
                </div>
            </form>
        </div>
    </div>
@endsection

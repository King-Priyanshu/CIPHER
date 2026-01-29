<x-layouts.admin>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Edit Page: {{ $page->title }}</h2>
    </div>

    <div class="bg-gray-800 rounded-lg shadow border border-gray-700 p-6">
        <form action="{{ route('admin.pages.update', $page) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-300 mb-2">Meta Title (SEO)</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="meta_description" class="block text-sm font-medium text-gray-300 mb-2">Meta Description (SEO)</label>
                    <input type="text" name="meta_description" id="meta_description" value="{{ old('meta_description', $page->meta_description) }}"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-600 rounded bg-gray-700">
                    <label for="is_published" class="ml-2 block text-sm text-gray-300">Published</label>
                </div>
            </div>

            <div class="mt-6">
                <label for="content" class="block text-sm font-medium text-gray-300 mb-2">Content (HTML)</label>
                <textarea name="content" id="content" rows="10" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm">{{ old('content', $page->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Page</button>
            </div>
        </form>
    </div>
</x-layouts.admin>

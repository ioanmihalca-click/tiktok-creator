<div class="pl-4 border-l">
    <div class="flex items-center gap-2 py-2">
        <span class="font-medium">{{ $category->name }}</span>
        <span class="text-sm text-gray-500">({{ $category->children_count }} subcategories)</span>
        @if (!$category->is_active)
            <span class="text-sm text-gray-500">(Inactive)</span>
        @endif
    </div>

    @if ($category->children->count())
        <div class="space-y-3">
            @foreach ($category->children as $child)
                @include('filament.resources.category-resource.pages.components.category-tree-branch', [
                    'category' => $child,
                ])
            @endforeach
        </div>
    @endif
</div>

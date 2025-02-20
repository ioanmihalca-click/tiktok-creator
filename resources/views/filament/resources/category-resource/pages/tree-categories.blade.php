<x-filament::page>
    <div class="space-y-6">
        @foreach ($this->getResource()::getModel()::defaultOrder()->get()->toTree() as $category)
            @include('filament.resources.category-resource.pages.components.category-tree-branch', [
                'category' => $category,
            ])
        @endforeach
    </div>
</x-filament::page>

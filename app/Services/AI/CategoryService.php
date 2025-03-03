<?php

namespace App\Services\AI;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Kalnoy\Nestedset\NodeNotFoundException;

class CategoryService
{
    public function __construct() {}

    public function getCategoryTree()
    {
        return Category::withDepth()->get()->toTree();
    }

    public function getCategoryBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function getCategoryFullPath(string $slug): ?string
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();

            // Get ancestors and self
            $path = $category->ancestorsAndSelf($category->id)
                ->pluck('name')
                ->implode(' > ');

            return $path;
        } catch (\Exception $e) {
            Log::error('Error getting category path:', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getPopularCategories(int $limit = 5)
    {
        return Category::withCount('videoProjects')
            ->orderByDesc('video_projects_count')
            ->limit($limit)
            ->get();
    }

    public function getCategories()
    {
        $categories = Category::withDepth()
            ->defaultOrder()
            ->get();


        return $categories->toTree();
    }

    // Metode pentru administrare (opțional)
    public function createCategory(array $data, ?int $parentId = null): Category
    {
        $category = new Category($data);

        if ($parentId) {
            $parent = Category::findOrFail($parentId);
            $category->appendToNode($parent)->save();
        } else {
            $category->saveAsRoot();
        }

        return $category;
    }

    public function updateCategoryMeta(string $slug, array $meta): void
    {
        Category::where('slug', $slug)->update(['meta' => $meta]);
    }

    public function generateUniqueSlug(string $name, ?int $parentId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

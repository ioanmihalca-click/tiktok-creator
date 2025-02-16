<?php

namespace App\Providers;

use App\Services\AI\CategoryService;
use App\Services\AI\ImageGenerationService;
use App\Services\AI\NarrationService;
use App\Services\AI\ScriptGenerationService;
use App\Services\AI\TopicGenerationService;
use App\Services\AI\VideoGenerationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService();
        });

        $this->app->singleton(TopicGenerationService::class);
        $this->app->singleton(ScriptGenerationService::class);
        $this->app->singleton(ImageGenerationService::class);
        $this->app->singleton(NarrationService::class);
        $this->app->singleton(VideoGenerationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

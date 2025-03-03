<?php

namespace App\Providers;

use App\Models\User;
use Laravel\Cashier\Cashier;
use App\Services\AI\CategoryService;
use App\Services\AI\NarrationService;
use Illuminate\Support\ServiceProvider;
use App\Services\AI\ImageGenerationService;
use App\Services\AI\TopicGenerationService;
use App\Services\AI\VideoGenerationService;
use App\Services\AI\ScriptGenerationService;

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
        Cashier::useCustomerModel(User::class);
    }
}

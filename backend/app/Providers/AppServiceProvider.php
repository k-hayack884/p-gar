<?php

namespace App\Providers;

use App\Domain\Repositories\PlantRepositoryInterface;
use App\Domain\Repositories\SiteRepositoryInterface;
use App\Domain\Repositories\WorkLogRepositoryInterface;
use App\Domain\Repositories\ZoneRepositoryInterface;
use App\Infrastructure\Models\Plant;
use App\Infrastructure\Models\Site;
use App\Infrastructure\Models\WorkLog;
use App\Infrastructure\Models\Zone;
use App\Infrastructure\Repositories\PlantEloquentRepository;
use App\Infrastructure\Repositories\SiteEloquentRepository;
use App\Infrastructure\Repositories\WorkLogEloquentRepository;
use App\Infrastructure\Repositories\ZoneEloquentRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SiteRepositoryInterface::class, SiteEloquentRepository::class);
        $this->app->bind(ZoneRepositoryInterface::class, ZoneEloquentRepository::class);
        $this->app->bind(PlantRepositoryInterface::class, PlantEloquentRepository::class);
        $this->app->bind(WorkLogRepositoryInterface::class, WorkLogEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'site' => Site::class,
            'zone' => Zone::class,
            'plant' => Plant::class,
            'work_log' => WorkLog::class,
        ]);
    }
}

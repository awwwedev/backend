<?php

namespace App\Providers;

use App\Models\News;
use App\Models\Realty;
use App\Models\RealtyType;
use App\Models\Slide;
use App\Observers\NewsObserver;
use App\Observers\RealtyObserver;
use App\Observers\RealtyTypeObserver;
use App\Observers\SlideObserver;
use Illuminate\Support\ServiceProvider;

class ObserversProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        RealtyType::observe(RealtyTypeObserver::class);
        News::observe(NewsObserver::class);
        Realty::observe(RealtyObserver::class);
        Slide::observe(SlideObserver::class);
    }
}

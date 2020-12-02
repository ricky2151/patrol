<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        date_default_timezone_set('Asia/Jakarta');

        $models = [
            'Auth'
        ];

        //binding repository
        foreach ($models as $model) {
            $this->app->bind("App\Repositories\Contracts\\{$model}RepositoryContract", "App\Repositories\Implementations\\{$model}RepositoryImplementation");
        }

        //binding services
        foreach ($models as $model) {
            $this->app->bind("App\Services\Contracts\\{$model}ServiceContract", "App\Services\Implementations\\{$model}ServiceImplementation");
        }


    }
}

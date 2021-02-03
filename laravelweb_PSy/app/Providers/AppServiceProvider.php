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
            'Auth',
            'Iot',
            'Building',
            'User',
            'Shift',
            'Photo',
            'History',
            'StatusNode'
        ];

        //binding repository
        foreach ($models as $model) {
            $this->app->singleton("App\Repositories\Contracts\\{$model}RepositoryContract", "App\Repositories\Implementations\\{$model}RepositoryImplementation");
        }

        //binding services
        foreach ($models as $model) {
            $this->app->singleton("App\Services\Contracts\\{$model}ServiceContract", "App\Services\Implementations\\{$model}ServiceImplementation");
        }


    }
}

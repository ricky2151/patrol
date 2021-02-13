<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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

        // DB::listen(function ($query) {
        //     error_log($query->sql);
            
        // });

        $models = [
            'Auth',
            'Iot',
            'Building',
            'User',
            'Shift',
            'Photo',
            'History',
            'StatusNode',
            'Floor',
            'Gateway',
            'Time',
            'Room',
            'Role'
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

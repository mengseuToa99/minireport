<?php

namespace Modules\MiniReportB1\Providers;

use App\Business;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Modules\MiniReportB1\Http\Services\DateFilterService;
use Illuminate\Support\Facades\View;

class MiniReportB1ServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'MiniReportB1';
    protected string $moduleNameLower = 'minireportb1';

    protected $middleware = [
        'MiniReportB1' => [
            'MiniReportB1Language' => 'ModuleLanguageMiddleware',
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        $this->registerTranslations();
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->registerMiddleware($this->app['router']);

        Blade::component('minireportb1::MiniReportB1.multitable.partials.visiblebutton', 'visiblebutton');
        // Register the sell-table component with the correct namespace

        View::composer('*', function ($view) {
            // Get the business ID from the session
            $business_id = session('user.business_id');
        
            // Default values
            $business_name = 'Default Business Name';
            $business_logo = null;
        
            // Fetch the business name and logo if business_id is set
            if ($business_id) {
                $business = Business::find($business_id);
                if ($business) {
                    $business_name = $business->name;
                    $business_logo = $business->logo ? '/uploads/business_logos/' . $business->logo : null;
                    
                  
                }
            }
        
            // Share the data with all views
            $view->with([
                'business_name' => $business_name,
                'business_logo' => $business_logo,
            ]);
        });
    
        $this->publishAssets();
    }

    protected function publishAssets()
    {
        $sourcePath = module_path($this->moduleName, 'Resources/assets');
        $targetPath = public_path('modules/' . $this->moduleNameLower);

        // Check if the source directory exists
        if (is_dir($sourcePath)) {
            // Create the target directory if it doesn't exist
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            // Publish the assets
            $this->publishes([
                $sourcePath => $targetPath,
                module_path($this->moduleName, 'Resources/assets/css/reusable-table.css') => public_path('modules/' . $this->moduleNameLower . '/css/reusable-table.css'),
                module_path($this->moduleName, 'Resources/assets/css/printjs.css') => public_path('modules/' . $this->moduleNameLower . '/css/printjs.css'),
                module_path($this->moduleName, 'Resources/assets/js/language-handler.js') => public_path('modules/' . $this->moduleNameLower . '/js/language-handler.js')

            ], 'assets');

            // In development, force publish the assets
            if (app()->environment('local')) {
                $this->publishOrLink($sourcePath, $targetPath);
            }
        }
    }

    protected function publishOrLink($source, $target)
    {
        if (is_dir($source)) {
            $this->copyDirectory($source, $target);
        }
    }

    protected function copyDirectory($source, $target)
    {
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $dir = $target . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
            } else {
                copy($item, $target . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * Register middleware.
     *
     * @param Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\Middleware\\{$middleware}";
                $router->aliasMiddleware($name, $class);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind('dateFilter', function ($app) {
            return new DateFilterService();
        });

    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands()
    {
        // Register any commands if needed.
        // $this->commands([]);
    }

    /**
     * Register command schedules.
     */
    protected function registerCommandSchedules()
    {
        // Register any command schedules if needed.
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations()
{
    $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

    if (is_dir($langPath)) {
        $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
    } else {
        $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
    }
}



    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php')
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register views.
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace') . '\\' . $this->moduleName . '\\' . config('modules.paths.generator.component-class.path'));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get the view paths to be published.
     *
     * @return array
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }

        return $paths;
    }
}

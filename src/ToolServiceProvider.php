<?php
namespace Badinansoft\TableFooter;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Badinansoft\TableFooter\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(NovaRequest $novaRequest)
    {

        Nova::script('table-footer', __DIR__.'/../dist/js/tool.js');
        Nova::style('table-footer', __DIR__.'/../dist/css/tool.css');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) use ($novaRequest) {
            Field::macro('calculate', function ($calculate,$title,$postfix='',$prefix='') use ($novaRequest) {
                return $this->withMeta(['calculate' => $calculate])
                    ->withMeta(['title' => $title])
                    ->withMeta([
                        'postfix' => $postfix,
                        'prefix' => $prefix
                    ]);
            });

        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
                ->prefix('nova-vendor/table-footer')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

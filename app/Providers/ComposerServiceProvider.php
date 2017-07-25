<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 5:20 PM
 */

namespace LeaseTracker\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Provides view composers for binding data to the view when it is rendered.
 * @package LeaseTracker\Providers
 */
class ComposerServiceProvider extends ServiceProvider
{

    /**
     * Register bindings to the container.
     *
     * @return void
     */
    public function boot()
    {
        // Want to trigger this on the default view which is extended by all other views.
        View::composer('layouts/default', 'LeaseTracker\Http\ViewComposers\ActiveMenuComposer');
    }

    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        //
    }


}
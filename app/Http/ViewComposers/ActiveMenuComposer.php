<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 5:23 PM
 */

namespace LeaseTracker\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Composer for injecting the active menu item onto the views.
 *
 * @package LeaseTracker\Providers
 */
class ActiveMenuComposer
{

    /**
     * @var \Illuminate\Http\Request $request The request object for getting the action.
     */
    protected $request;

    /**
     * ActiveMenuComposer constructor.
     * @param Request $request The request object.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Compose the active menu item information onto the View.
     * @param View $view the view.
     */
    public function compose(View $view) {
        $routeAction = $this->request->route()->getAction();
        $activeMenu = $routeAction['_active_menu'] ?? '/';
        $view->with('_active_menu', $activeMenu);
    }
}
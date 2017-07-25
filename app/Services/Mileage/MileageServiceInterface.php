<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 1:08 PM
 */

namespace LeaseTracker\Services\Mileage;


use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use LeaseTracker\Http\Requests\CreateMileageFormRequest;

interface MileageServiceInterface
{
    /**
     * Build the create mileage page.
     *
     * @param int $vehicleId The id of the vehicle for which you are adding an entry.
     * @return View|RedirectResponse The create page view or a redirect on error.
     */
    function buildCreatePage(int $vehicleId);

    /**
     * Store the mileage entry on the datasource.
     *
     * @param CreateMileageFormRequest $request The request from the create page.
     * @param int $id The vehicle id for the mile entry.
     * @return RedirectResponse A redirect to either the mileage entry page (for adding additional miles) or back to the form
     * if an error has occured.
     */
    function storeMileage(CreateMileageFormRequest $request, int $id);

}
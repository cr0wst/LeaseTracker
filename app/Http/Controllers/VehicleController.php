<?php

namespace LeaseTracker\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use LeaseTracker\Http\Requests\CreateVehicleFormRequest;
use LeaseTracker\Services\Vehicle\VehicleServiceInterface;

/**
 * Controller for the vehicle portion of the application.
 * @package LeaseTracker\Http\Controllers
 */
class VehicleController extends Controller
{
    /**
     * @var VehicleServiceInterface $vehicleService Reference to the VehicleService.
     */
    private $vehicleService;


    /**
     * Constructor.
     * @param VehicleServiceInterface $vehicleService The injected vehicle service.
     */
    public function __construct(VehicleServiceInterface $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Method for handling the root of the vehicle routes.
     * @return View the index page view.
     */
    public function index()
    {
        // It might be redundant to call off to the service for index generation, but in a Spring-based system
        // this is similar to what I would do.
        // Controller
        //      -> Service (Page Level, sometimes called an Application Service)
        //      -> Multiple Services (Repos, Business Layers, External Services, etc..)
        // Sometimes the Controller can fit the role of the first Service, but I find that doesn't promote testability
        // or reusability at least in Java (controllers are more difficult to test in Java).
        return $this->vehicleService->buildIndexPage();
    }

    /**
     * Method for handling showing of the vehicle details page.
     * @param int $id The vehicle to show.
     * @return View|RedirectResponse The vehicle details page view or a redirect.
     */
    public function show(int $id)
    {
        return $this->vehicleService->buildShowPage($id);
    }

    /**
     * Method for handling the showing of the vehicle edit page.
     * @param int $id The vehicle id to edit.
     * @return View The vehicle edit page view.
     */
    public function edit(int $id)
    {
        return $this->vehicleService->buildEditPage($id);
    }

    /**
     * Method for handling the showing of the vehicle create page.
     * @return View The vehicle create page view.
     */
    public function create()
    {
        return $this->vehicleService->buildCreatePage();
    }

    /**
     * Method for storing a new vehicle to the datasource.
     * @param CreateVehicleFormRequest $request The form request which has been validated.
     * @return RedirectResponse Redirect to the appropriate page depending on success or failure.
     */
    public function store(CreateVehicleFormRequest $request)
    {
        return $this->vehicleService->storeVehicle($request);
    }

    /**
     * Method for updating a new vehicle to the datasource.
     *
     * @param CreateVehicleFormRequest $request The form request which has been validated.
     * @param int $id The vehicle id to update.
     * @return RedirectResponse Redirect to the appropriate page depending on success or failure.
     */
    public function update(CreateVehicleFormRequest $request, int $id)
    {
        return $this->vehicleService->updateVehicle($request, $id);
    }

    /**
     * Route for removing a vehicle from the datasource.
     * 
     * @param int $id The vehicle id to remove.
     * @return mixed
     */
    public function destroy(int $id)
    {
        return $this->vehicleService->destroyVehicle($id);
    }
}
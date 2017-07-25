<?php

namespace LeaseTracker\Services\Vehicle;

use Illuminate\View\View;
use LeaseTracker\Http\Requests\CreateVehicleFormRequest;

/**
 * Provides functionality for vehicle management.
 * @package LeaseTracker\Vehicle
 */
interface VehicleServiceInterface
{
    // Page Builder Methods

    /**
     * Build the index page.
     *
     * @return View The index page view.
     */
    function buildIndexPage();

    /**
     * Build the show vehicle page.
     * @param int $id The ID for the vehicle to show.
     * @return View The show page view.
     */
    function buildShowPage(int $id);

    /**
     * Build the edit vehicle page.
     *
     * @param int $id The ID for the vehicle to edit.
     * @return View The edit page view.
     */
    function buildEditPage(int $id);

    /**
     * Build the create vehicle page.
     *
     * @return View The create page view.
     */
    function buildCreatePage();

    // Non Page Builder Methods

    /**
     * Validate and store the vehicle to the datasource.
     * @param CreateVehicleFormRequest $request The request from the form.
     * @return mixed
     */
    function storeVehicle(CreateVehicleFormRequest $request);

    /**
     * Validate and update the vehicle on the datasource.
     * @param CreateVehicleFormRequest $request The request from the form.
     * @param int $id The id of the vehicle to update.
     * @return mixed
     */
    function updateVehicle(CreateVehicleFormRequest $request, int $id);

    /**
     * Destroy the passed in vehicle.
     * @param int $id The vehicle id to be destroyed.
     * @return mixed
     */
    function destroyVehicle(int $id);
}
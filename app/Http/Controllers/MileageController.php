<?php

namespace LeaseTracker\Http\Controllers;

use LeaseTracker\Http\Requests\CreateMileageFormRequest;
use LeaseTracker\Services\Mileage\MileageServiceInterface;

/**
 * Controller for the mileage portion of the application.
 *
 * @package LeaseTracker\Http\Controllers
 */
class MileageController extends Controller
{
    /**
     * @var MileageServiceInterface $mileageService Reference to the MileageService.
     */
    private $mileageService;

    /**
     * MileageController constructor.
     * @param MileageServiceInterface $mileageService The injected mileage service.
     */
    public function __construct(MileageServiceInterface $mileageService)
    {
        $this->mileageService = $mileageService;
    }

    /**
     * Route for the mile entry create page.
     *
     * @param int $id The vehicle id to add the mileage to.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Redirection back to the vehicle.show
     * route on success, or the entry route on error.
     */
    public function create(int $id)
    {
        return $this->mileageService->buildCreatePage($id);
    }

    /**
     * Route for storing the mile entry on the datasource.
     *
     * @param CreateMileageFormRequest $request The form values on a request.
     * @param int $id The vehicle to update.
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse Redirection back to the vehicle.show
     * route on success, or the entry route on error.
     */
    public function store(CreateMileageFormRequest $request, int $id)
    {
        return $this->mileageService->storeMileage($request, $id);
    }
}

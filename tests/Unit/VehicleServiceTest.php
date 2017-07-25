<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LeaseTracker\Http\Requests\CreateVehicleFormRequest;
use LeaseTracker\MileEntry;
use LeaseTracker\Repositories\MileEntryRepository;
use LeaseTracker\Repositories\VehicleRepository;
use LeaseTracker\Repositories\VehicleRepositoryInterface;
use LeaseTracker\Services\Calculation\CalculationService;
use LeaseTracker\Services\Calculation\CalculationServiceInterface;
use LeaseTracker\Services\Vehicle\VehicleService;
use LeaseTracker\Services\VehicleImage\GoogleVehicleImageService;
use LeaseTracker\Services\VehicleImage\VehicleImageServiceInterface;
use LeaseTracker\Vehicle;
use Mockery;
use Tests\TestCase;

class VehicleServiceTest extends TestCase
{

    /**
     * @var CalculationServiceInterface $calculationService reference to the calculation service.
     */
    private $calculationService;

    /**
     * @var VehicleImageServiceInterface reference to the vehicle image service.
     */
    private $vehicleImageService;

    /**
     * @var VehicleRepositoryInterface reference to the vehicle repository.
     */
    private $vehicleRepository;

    /**
     * @var MileEntryRepository reference to the mile entry repository.
     */
    private $mileageRepository;

    /**
     * Setup for testing.  Generate the Mocks and shared expectations.
     */
    public function setUp()
    {
        parent::setUp();

        // No need to mock this service, it can be used as-is.
        $this->calculationService = new CalculationService;

        // Expectation for the Vehicle Image Service doesn't need to change for each test just use the dummy image.
        $this->vehicleImageService = Mockery::mock(GoogleVehicleImageService::class);
        $this->vehicleImageService
            ->shouldReceive('retrieveImageByName')
            ->withAnyArgs()
            ->andReturn(GoogleVehicleImageService::DEFAULT_IMAGE_LOCATION);

        // Mock both repositories so the data can be manipulated.
        $this->vehicleRepository = Mockery::mock(VehicleRepository::class);
        $this->mileageRepository = Mockery::mock(MileEntryRepository::class);
    }

    /**
     * Tear everything down.
     */
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * Test that the index page view is built correctly when zero, one, and many vehicles are returned from the datasource.
     */
    public function testBuildIndexPageVariousVehicles()
    {
        // I was having issues getting collections of various sizes, using the factory made this easier.
        $noVehiclesReturned = factory(Vehicle::class, 0)->make();
        $oneVehicleReturned = factory(Vehicle::class, 1)->make();
        $manyVehiclesReturned = factory(Vehicle::class, 5)->make();

        // Need an empty MileEntry collection for no Vehicle case, any collection is fine for single or multi-vehicles.
        $noVehiclesMileage = factory(MileEntry::class, 0)->make();
        $oneOrManyVehicleMileage = factory(MileEntry::class, 10)->make();

        // Seed the vehicle ids.  I know create() takes an array of overrides, but I can't seem to get make() to take one.
        $oneVehicleReturned[0]->id = 1;
        for ($i = 0; $i < count($manyVehiclesReturned); $i++) {
            $manyVehiclesReturned[$i]->id = $i + 1;
        }

        $this->vehicleRepository
            ->shouldReceive('retrieveVehicleList')
            ->withNoArgs()
            ->andReturn(
                $noVehiclesReturned,
                $oneVehicleReturned,
                $manyVehiclesReturned);

        $this->mileageRepository
            ->shouldReceive('retrieveMileageForVehicle')
            ->withAnyArgs()
            // For 0 vehicles, generate 0 mile-entry entries.
            ->andReturn(
                $noVehiclesMileage,
                $oneOrManyVehicleMileage,
                $oneOrManyVehicleMileage
            );

        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);

        // First call will trigger for no vehicles
        $returnedViewNoVehicles = $service->buildIndexPage();
        $returnedViewOneVehicle = $service->buildIndexPage();
        $returnedViewManyVehicles = $service->buildIndexPage();

        // Correct View
        $this->assertEquals('pages.vehicle.index', $returnedViewNoVehicles->getName());
        $this->assertEquals('pages.vehicle.index', $returnedViewOneVehicle->getName());
        $this->assertEquals('pages.vehicle.index', $returnedViewManyVehicles->getName());

        // Correct vehicles
        $this->assertEquals($noVehiclesReturned, $returnedViewNoVehicles->getData()['vehicles']);
        $this->assertEquals($oneVehicleReturned, $returnedViewOneVehicle->getData()['vehicles']);
        $this->assertEquals($manyVehiclesReturned, $returnedViewManyVehicles->getData()['vehicles']);
    }

    /**
     * Test that the show page functions when a vehicle is found.  Considers both a vehicle with mile entries and a vehicle without.
     */
    public function testBuildShowPageWithVehicle()
    {
        $vehicles = factory(Vehicle::class, 2)->make();
        $vehicles[0]->id = 1;
        $vehicles[1]->id = 2;

        // Vehicle with no Mileage
        $this->vehicleRepository
            ->shouldReceive('retrieveVehicle')
            ->with(1)
            ->andReturn($vehicles[0]);

        // Vehicle with many Mileage
        $this->vehicleRepository
            ->shouldReceive('retrieveVehicle')
            ->with(2)
            ->andReturn($vehicles[1]);

        $noMileage = factory(MileEntry::class, 0)->make();
        $manyMileage = factory(MileEntry::class, 20)->make();

        $this->mileageRepository
            ->shouldReceive('retrieveMileageForVehicle')
            ->with(1)
            ->andReturn($noMileage);
        $this->mileageRepository
            ->shouldReceive('retrieveMileageForVehicle')
            ->with(2)
            ->andReturn($manyMileage);

        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);

        // Check vehicle without mileage
        $returnedView = $service->buildShowPage(1);

        // Correct View
        $this->assertEquals('pages.vehicle.show', $returnedView->getName());

        // Correct Vehicle
        $this->assertEquals($vehicles[0], $returnedView->getData()['vehicle']);

        // Correct Mile Entries
        $this->assertEquals($noMileage, $returnedView->getData()['mileEntries']);

        // Check vehicle with mileage
        $returnedView = $service->buildShowPage(2);

        // Correct View
        $this->assertEquals('pages.vehicle.show', $returnedView->getName());

        // Correct Vehicle
        $this->assertEquals($vehicles[1], $returnedView->getData()['vehicle']);

        // Correct Mile Entries
        $this->assertEquals($manyMileage, $returnedView->getData()['mileEntries']);
    }

    /**
     * Verify that when showing a vehicle that does not exist, the application redirects the user appropriately.
     */
    public function testBuildShowPageWithNoVehicle()
    {
        $this->vehicleRepository
            ->shouldReceive('retrieveVehicle')
            ->withAnyArgs()
            ->andThrow(ModelNotFoundException::class);

        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);
        $returnedRedirect = $service->buildShowPage(1);

        // Note: One of the down-sides of returning a redirect from a service to the controller instead of testing
        // the controller seems to be that I cannot use some of the sugar-methods like assertRedirectedTo.
        // I could probably switch this and the other redirect checks to a controller tester, but this should be fine.

        // 302 = Http Redirect
        $this->assertEquals(302, $returnedRedirect->getStatusCode());

        // Make sure the message is put on the session.
        $this->assertEquals(VehicleService::VEHICLE_NOT_FOUND_MESSAGE, $returnedRedirect->getSession()->get('message'));

        // Check that the correct route was picked.
        $this->assertEquals(route('vehicle.index'), $returnedRedirect->getTargetUrl());
    }

    /**
     * Verify that the edit page retrieves the correct vehicle.
     */
    public function testBuildEditPageWithVehicle()
    {
        $vehicle = factory(Vehicle::class)->make();
        $vehicle->id = 1;

        // Vehicle with no Mileage
        $this->vehicleRepository
            ->shouldReceive('retrieveVehicle')
            ->withAnyArgs()
            ->andReturn($vehicle);

        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);

        // Check vehicle without mileage
        $returnedView = $service->buildEditPage($vehicle->id);

        // Correct View
        $this->assertEquals('pages.vehicle.edit', $returnedView->getName());

        // Correct Vehicle
        $this->assertEquals($vehicle, $returnedView->getData()['vehicle']);
    }


    /**
     * Verify that when a vehicle is not found the edit page will redirect correctly.
     */
    public function testBuildEditPageWithNoVehicle()
    {
        $this->vehicleRepository
            ->shouldReceive('retrieveVehicle')
            ->withAnyArgs()
            ->andThrow(ModelNotFoundException::class);

        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);
        $returnedRedirect = $service->buildEditPage(1);

        // Note: One of the down-sides of returning a redirect from a service to the controller instead of testing
        // the controller seems to be that I cannot use some of the sugar-methods like assertRedirectedTo.
        // I could probably switch this and the other redirect checks to a controller tester, but this should be fine.

        // 302 = Http Redirect
        $this->assertEquals(302, $returnedRedirect->getStatusCode());

        // Make sure the message is put on the session.
        $this->assertEquals(VehicleService::VEHICLE_NOT_FOUND_MESSAGE, $returnedRedirect->getSession()->get('message'));

        // Check that the correct route was picked.
        $this->assertEquals(route('vehicle.index'), $returnedRedirect->getTargetUrl());
    }

    /**
     * Verify that the create page gets built.
     */
    public function testBuildCreatePage()
    {
        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);
        $returnedView = $service->buildCreatePage();

        $this->assertEquals('pages.vehicle.create', $returnedView->getName());
    }

    /**
     * Test the store functionality of the Service.
     * TODO: Fix this test to use a test datasource as it does not work fully.
     */
    public function testStoreVehicle() {
        $vehicle = factory(Vehicle::class)->make();

        $this->vehicleRepository
            ->shouldReceive('save')
            ->withAnyArgs()
            ->andReturn(true);

        $request = CreateVehicleFormRequest::create('dummyuri', 'POST', $vehicle->toArray());

        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);

        $returnedRedirect = $service->storeVehicle($request);

        // 302 = Http Redirect
        $this->assertEquals(302, $returnedRedirect->getStatusCode());

        // Make sure the message is put on the session.
        $this->assertEquals(VehicleService::VEHICLE_CREATED_MESSAGE, $returnedRedirect->getSession()->get('message'));

        // Check that the correct route was picked.  I played with this a few times and couldn't find a way to simulate it
        // The route that comes back from the redirect has the new vehicle id on the end of it.  However, that ID isn't built
        // until after the model object is saved.

        // I couldn't figure out how to mock the repo so that it modifies the passed in parameter and attaches an id.
        // TODO: Investigate parameter mutations with Mockery for better testing of this redirect.  This may be a good use for functional testing.
        $this->assertEquals(route('vehicle.show', null), $returnedRedirect->getTargetUrl());
    }

    /**
     * Test that the update vehicle functionality of the service works.
     */
    public function testUpdateVehicle() {
        // This method is going to run into the same limitations of the last method when it comes to saves.  I'm going to ONLY
        // test the exception routes.

        $vehicle = factory(Vehicle::class)->make();

        $this->vehicleRepository
            ->shouldReceive('retrieveVehicle')
            ->with(1)
            ->andReturn($vehicle);

        $this->vehicleRepository
            ->shouldReceive('save')
            ->withAnyArgs()
            ->andThrow(Exception::class);

        $service = new VehicleService($this->calculationService, $this->vehicleImageService, $this->vehicleRepository, $this->mileageRepository);
        $request = CreateVehicleFormRequest::create('dummyuri', 'POST', $vehicle->toArray());

        $returnedRedirect = $service->updateVehicle($request, 1);

        // 302 = Http Redirect
        $this->assertEquals(302, $returnedRedirect->getStatusCode());

        // Make sure the error message is on the session.  This isn't the cleanest way to do it I'm sure.
        $returnedMessage = $returnedRedirect->getSession()->get('errors')->getBag('default')->get('message')[0];
        $this->assertEquals(VehicleService::VEHICLE_SAVE_ERROR_MESSAGE, $returnedMessage);
    }
}

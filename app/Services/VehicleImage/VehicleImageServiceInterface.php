<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 4:36 PM
 */

namespace LeaseTracker\Services\VehicleImage;

/**
 * Provides a service for getting vehicle images.
 * @package LeaseTracker\Services\VehicleImage
 */
interface VehicleImageServiceInterface
{

    /**
     * Retrieve a vehicle image from a name.
     * @param string $name The name of the image to retrieve.
     * @return string The URL to the image.
     */
    function retrieveImageByName(string $name):string;
}
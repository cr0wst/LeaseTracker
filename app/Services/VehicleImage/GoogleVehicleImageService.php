<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 4:41 PM
 */

namespace LeaseTracker\Services\VehicleImage;
use DOMDocument;

/**
 * Service for retrieving images from Google Images.  NOTE (BIG NOTE): Not to be used as a production solution!
 * Google wouldn't be very happy if we were going to do this on a regular basis.  However, for the purposes of this
 * example app, I'm willing to take the blame.
 *
 * @package LeaseTracker\Services\VehicleImage
 */
class GoogleVehicleImageService implements VehicleImageServiceInterface
{
    /**
     * The default image location.
     */
    const DEFAULT_IMAGE_LOCATION = '/dummy.png';

    /**
     * The alt tag on the img tag which makes it eligible for retrieval.
     */
    const CORRECT_IMAGE_ALT_TAG = 'Image result for';
    /**
     * Retrieve a vehicle image from a name from Google images.  The method will make a request using curl to a url
     * and come back with the first result's image url.  There are several ways I can imagine this failing:
     * 1) Website with the image doesn't allow remote linking.  This will result in a broken image.
     * 2) Future URL changes to the Google image search page could break this service.
     * 3) Google could get really mad because it's not an actual api and shut down the service.
     *
     * This was just a fun little addition I wanted to try out, for production purposes I would probably find a
     * legit API to use.
     *
     * @param string $name The name of the image to retrieve.
     * @return string The URL to the image for hotlinking.
     */
    function retrieveImageByName(string $name):string
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, "http://www.google.com/search?q=".str_replace(" ", "+", $name)."&safe=on&tbm=isch");
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($curl_handle);
        curl_close($curl_handle);

        $doc = new DOMDocument();
        // If this fails, give them a dummy image, suppressing Errors here and just going to check if the image was successful or not.
        // We can recover from any errors by forcing a dummy image.
        if (@$doc->loadHTML($contents)) {

            $tags = $doc->getElementsByTagName('img');

            // Find the first image tag that has the alt text implying it's a result.
            foreach ($tags as $tag) {
                if (strpos($tag->getAttribute('alt'), self::CORRECT_IMAGE_ALT_TAG) !== false) {
                    return $tag->getAttribute('src');
                }
            }
        }

        // Either the HTML is invalid or no images found.
        return self::DEFAULT_IMAGE_LOCATION;
    }
}
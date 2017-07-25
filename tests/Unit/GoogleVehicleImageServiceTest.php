<?php

namespace LeaseTracker\Services\VehicleImage;

use Tests\TestCase;

// I thought about making a wrapper class for curl which is what I would normally do.  However, I read an interesting post
// about using namespaces to mock system functions and thought it would be fun to try.
// https://www.schmengler-se.de/en/2011/03/php-mocking-built-in-functions-like-time-in-unit-tests/

// Cons: I don't like the idea of polluting the namespace but, to be fair, I would do this in Java with my unit tests.
// In Java it's very common for your tests to reside in the same package as the testable because it allows you to
// test methods that have package visibility.

// Pro: Don't need to create a wrapper class.

/**
 * Stub for the curl_init method.
 */
function curl_init() {
    return true;
}
/**
 * A stub for the curl_exec method that will be called from the GoogleVehicleImageService.  Allows for various test cases
 * without wrapping the curl functions.
 * @return The contents of GoogleVehicleImageServiceTest::$htmlContents;
 */

/**
 * Stub for curl_exec.
 */
function curl_exec($handle) {
    return GoogleVehicleImageServiceTest::$htmlContents;
}

/**
 * Stub for curl_close.
 */
function curl_close($handler) {
    return true;
}

/**
 * Stub for curl_setopt.
 */
function curl_setopt($ch, $option, $value) {
    return true;
}

class GoogleVehicleImageServiceTest extends TestCase
{
    /**
     * @var GoogleVehicleImageService $service reference to the GoogleVehicleImageService
     */
    private $service;

    /**
     * @var string $htmlContents The contents that curl_exec will return.
     */
    public static $htmlContents;

    /**
     * Initialize the service.
     */
    public function setUp()
    {
        parent::setUp();
        $this->service = new GoogleVehicleImageService;
    }

    public function tearDown() {
        self::$htmlContents = null;
    }

    /**
     * Verify that when given multiple image tags, only the first one is parsed for its source.
     */
    public function testRetrieveImageByNameFavorsFirstImage()
    {
        $expectedImage = 'YouFoundMe';
        self::$htmlContents = $this->buildImageHtmlTag($expectedImage) . $this->buildImageHtmlTag('testSource2');

        $this->assertEquals($expectedImage, $this->service->retrieveImageByName("test"));
    }

    /**
     * Verify that when given a single image tag, it is still parsed.
     */
    public function testRetrieveImageByNameSingleImage()
    {
        $expectedImage = 'YouFoundMe';
        self::$htmlContents = $this->buildImageHtmlTag($expectedImage);

        $this->assertEquals($expectedImage, $this->service->retrieveImageByName('test'));
    }

    /**
     * Verify that when the correct image is surrounded by incorrect images, the first one still gets retrieved.
     */
    public function testRetrieveImageByNameMultipleResultsVariousAltTags() {
        $expectedImage = 'YouFoundMe';
        self::$htmlContents = $this->buildImageHtmlTag('testSource1', 'Cool alt tag!')
            . $this->buildImageHtmlTag('testSource2', 'Another cool alt tag!')
            . $this->buildImageHtmlTag($expectedImage)
            . $this->buildImageHtmlTag('testSource4')
            . $this->buildImageHtmlTag('testSource5', 'Sneaky alt tags');

        $this->assertEquals($expectedImage, $this->service->retrieveImageByName('test'));
    }

    /**
     * Verify that when no image tags are found, a default image will appear.
     */
    public function testRetrieveImageByNameNoImageTags()
    {
        self::$htmlContents = '<body><div><h1>Test!</h1></div>';

        $this->assertEquals(GoogleVehicleImageService::DEFAULT_IMAGE_LOCATION, $this->service->retrieveImageByName('test'));
    }

    /**
     * Verify that when no eligible images are found, a default image will appear.
     */
    public function testRetrieveImageByNameNoEligibleImages()
    {
        self::$htmlContents = $this->buildImageHtmlTag('CantFindMe', 'Cool alt text');

        $this->assertEquals(GoogleVehicleImageService::DEFAULT_IMAGE_LOCATION, $this->service->retrieveImageByName('test'));
    }

    /**
     * Verify that when the string passed in is not HTML, a default image will appear.
     */
    public function testRetrieveImageByNameNotValidHtml() {
        self::$htmlContents = '{html}{img}';

        $this->assertEquals(GoogleVehicleImageService::DEFAULT_IMAGE_LOCATION, $this->service->retrieveImageByName('test'));
    }

    /**
     * Helper method for building a standard HTML image tag with an alt and source.
     *
     * @param string $source The source URL.
     * @param string $alt The alt text, by default it is 'Image result for'
     * @return string The HTML tag with source and alt attributes.
     */
    private function buildImageHtmlTag($source, $alt = 'Image result for') {
        return '<img alt="' . $alt . '" src="' . $source . '">';
    }
}

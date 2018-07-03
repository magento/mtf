<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Driver\Selenium\Driver;

use Magento\Mtf\Client\Driver\Selenium\RemoteDriver;

/**
 * Driver for waiting page load.
 */
class PageLoader implements PageLoaderInterface
{
    /**
     * Tracker variable for the current browser page
     *
     * @var string
     */
    private static $previousUrl = '';

    /**
     * Tracker variable for the active jQuery ajax count
     *
     * @var int
     */
    protected static $previousJqAjax = 0;

    /**
     * Test metadata variable to track sequential readiness check failures for the same jquery ajax request count
     * Value is reset to 0 when the current page url changes
     *
     * @var int
     */
    protected static $jqAjaxFailures = 0;

    /**
     * Tracker variable for the active prototype.js ajax count
     *
     * @var int
     */
    protected static $previousPrototypeAjax = 0;


    /**
     * Test metadata variable to track sequential readiness check failures for the same prototype.js ajax request count
     * Value is reset to 0 when the current page url changes
     *
     * @var int
     */
    protected static $prototypeAjaxFailures = 0;

    /**
     * Remote driver instance.
     *
     * @var RemoteDriver
     */
    private $driver;

    /**
     * Set driver.
     *
     * @param RemoteDriver $driver
     * @return $this
     */
    public function setDriver(RemoteDriver $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Wait for page to load.
     *
     * @throws \Exception
     * @return void
     */
    public function wait()
    {
        $driver = $this->driver;
        try {
            $driver->waitUntil(
                function () use ($driver) {
                    $result = $driver->execute(['script' => "return document['readyState']", 'args' => []]);
                    return $result === 'complete' || $result === 'uninitialized';
                }
            );
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf('Error occurred during waiting for document readyState. Message: "%s"', $e->getMessage())
            );
        }

        $url = $driver->url();
        if ($url != PageLoader::$previousUrl) {
            PageLoader::$previousJqAjax = 0;
            PageLoader::$jqAjaxFailures = 0;
            PageLoader::$previousPrototypeAjax = 0;
            PageLoader::$prototypeAjaxFailures = 0;
            PageLoader::$previousUrl = $url;
        }

        try {
            $driver->waitUntil([$this, 'isPageReady']);
            PageLoader::$previousJqAjax = 0;
            PageLoader::$jqAjaxFailures = 0;
            PageLoader::$previousPrototypeAjax = 0;
            PageLoader::$prototypeAjaxFailures = 0;
        } catch (\Exception $e) {
            $failsBeforeReset = isset($_ENV['readiness_failure_threshold']) ? $_ENV['readiness_failure_threshold'] : 3;

            // Check if jQuery ajax count failed on the same value, which can happen if an exception is
            // thrown during an ajax callback causing the active request count to not decrement
            $jqAjax = intval($driver->execute([
                'script' =>
                    'if (!!window.jQuery) {
                        return window.jQuery.active;
                    }
                    return 0;',
                'args' => []
            ]));

            if ($jqAjax == PageLoader::$previousJqAjax) {
                PageLoader::$jqAjaxFailures++;
            }
            else {
                PageLoader::$jqAjaxFailures = 1;
                PageLoader::$previousJqAjax = $jqAjax;
            }

            if (PageLoader::$jqAjaxFailures >= $failsBeforeReset) {
                $driver->execute(['script' => "if (!!window.jQuery) { window.jQuery.active = 0; }", 'args' => []]);
                PageLoader::$jqAjaxFailures = 0;
            }

            // Check if prototype.js ajax count failed on the same value, which can happen if an exception is
            // thrown during an ajax callback causing the active request count to not decrement
            $prototypeAjax = intval($driver->execute([
                'script' =>
                    'if (!!window.Prototype) {
                        return window.Ajax.activeRequestCount;
                    }
                    return 0;',
                'args' => []
            ]));

            if ($prototypeAjax == PageLoader::$previousPrototypeAjax) {
                PageLoader::$prototypeAjaxFailures++;
            }
            else {
                PageLoader::$prototypeAjaxFailures = 1;
                PageLoader::$previousPrototypeAjax = $prototypeAjax;
            }

            if (PageLoader::$prototypeAjaxFailures >= $failsBeforeReset) {
                $driver->execute([
                    'script' => "if (!!window.Prototype) { window.Ajax.activeRequestCount = 0; }",
                    'args' => []
                ]);
                PageLoader::$prototypeAjaxFailures = 0;
            }
        }
    }

    /**
     * Checks active ajax requests and require.js module registry queue to see if the page is ready
     *
     * @return bool
     */
    private function isPageReady() {
        $ready = true;
        $driver = $this->driver;

        // jQuery ajax requests
        $jqAjax = intval($driver->execute([
            'script' =>
                'if (!!window.jQuery) {
                    return window.jQuery.active;
                }
                return 0;',
            'args' => []
        ]));
        if ($jqAjax > 0) {
            $ready = false;
        }
        else {
            PageLoader::$previousJqAjax = 0;
            PageLoader::$jqAjaxFailures = 0;
        }

        // prototype.js ajax requests
        $prototypeAjax = intval($driver->execute([
            'script' =>
                'if (!!window.Prototype) {
                    return window.Ajax.activeRequestCount;
                }
                return 0;',
            'args' => []
        ]));
        if ($prototypeAjax > 0) {
            $ready = false;
        }
        else {
            PageLoader::$previousPrototypeAjax = 0;
            PageLoader::$prototypeAjaxFailures = 0;
        }

        // require.js module definitions
        $activeDefinitionScript =
            'if (!window.requirejs) {
                return null;
            }
            var contexts = window.requirejs.s.contexts;
            for (var label in contexts) {
                if (contexts.hasOwnProperty(label)) {
                    var registry = contexts[label].registry;
                    for (var module in registry) {
                        if (registry.hasOwnProperty(module) && registry[module].enabled) {
                            return module;
                        }
                    }
                }
            }
            return null;';
        $moduleInProgress = $driver->execute(['script' => $activeDefinitionScript, 'args' => []]);
        if ($moduleInProgress === 'null') {
            $moduleInProgress = null;
        }
        if (!is_null($moduleInProgress)) {
            $ready = false;
        }

        return $ready;
    }
}

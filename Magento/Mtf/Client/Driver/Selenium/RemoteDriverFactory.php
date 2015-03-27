<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client\Driver\Selenium;

use Magento\Mtf\ObjectManager;

/**
 * Class RemoteDriverFactory
 */
class RemoteDriverFactory
{
    /**
     * Class name
     */
    const CLASS_NAME = 'Magento\Mtf\Client\Driver\Selenium\RemoteDriver';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Selenium driver instance
     *
     * @return \Magento\Mtf\Client\Driver\Selenium\RemoteDriver
     */
    public function create()
    {
        return $this->objectManager->create(static::CLASS_NAME);
    }
}

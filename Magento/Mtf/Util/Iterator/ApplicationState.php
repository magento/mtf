<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Iterator;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\Config\DataInterface;

/**
 * Class ApplicationState
 *
 * @api
 */
class ApplicationState extends AbstractIterator
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Test Runner Configuration object
     *
     * @var DataInterface
     */
    protected $configData;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param DataInterface $configData
     */
    public function __construct(
        ObjectManager $objectManager,
        DataInterface $configData
    ) {
        $this->objectManager = $objectManager;
        $this->configData = $configData;

        $this->data = $this->getAppStates();
        $this->initFirstElement();
    }

    /**
     * Get current element
     *
     * @return array
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Check if current element is valid
     *
     * @return boolean
     */
    protected function isValid()
    {
        return true;
    }

    /**
     * Get Available Application State Objects
     *
     * @return \Magento\Mtf\App\State\StateInterface[]
     */
    protected function getAppStates()
    {
        $states = [];
        $statePathes = glob(MTF_STATES_PATH . 'State[0-9]*');

        foreach ($statePathes as $key => $path) {
            $states[] = [
                'class' => 'Magento\Mtf\\App\\State\\' . basename($path, ".php"),
                'name' => 'Application Configuration Profile ' . ($key + 1),
                'arguments' => []
            ];
        }

        return $states;
    }
}

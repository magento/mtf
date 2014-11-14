<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mtf\Util\Iterator;

use Mtf\ObjectManager;
use Mtf\TestRunner\Configuration;

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
     * @var \Mtf\TestRunner\Configuration
     */
    protected $testRunnerConfig;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Configuration $testRunnerConfig
     */
    public function __construct(
        ObjectManager $objectManager,
        Configuration $testRunnerConfig
    ) {
        $this->objectManager = $objectManager;
        $this->testRunnerConfig = $testRunnerConfig;

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
     * @return \Mtf\App\State\StateInterface[]
     */
    protected function getAppStates()
    {
        $states = [];
        $statePathes = glob(MTF_STATES_PATH . 'State[0-9]*');

        foreach ($statePathes as $key => $path) {
            $states[] = [
                'class' => 'Mtf\\App\\State\\' . basename($path, ".php"),
                'name' => 'Application Configuration Profile ' . ($key + 1),
                'arguments' => []
            ];
        }

        return $states;
    }
}

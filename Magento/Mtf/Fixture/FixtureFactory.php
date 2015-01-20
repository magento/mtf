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

namespace Magento\Mtf\Fixture;

use Magento\Mtf\Factory\AbstractFactory;
use Magento\Mtf\Configuration\Reader;
use Magento\Mtf\ObjectManager;

/**
 * Factory for Fixtures
 *
 * @api
 */
class FixtureFactory extends AbstractFactory
{
    /**
     * Fixtures definition array
     *
     * @var array
     */
    protected $configuration;

    /**
     * Generated factory entity name
     *
     * @var string
     */
    protected $factoryName = 'Fixture';

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Reader $fixtureReader
     */
    public function __construct(
        ObjectManager $objectManager,
        Reader $fixtureReader
    ) {
        parent::__construct($objectManager);
        $this->configuration = $fixtureReader->read('fixture');
    }

    /**
     * Create fixture by its code
     *
     * @param string $code
     * @param array $arguments
     * @return FixtureInterface
     */
    public function createByCode($code, array $arguments = [])
    {
        $class = $this->resolveClassName($code);
        return $this->create($class, $arguments);
    }

    /**
     * Resolve class name
     *
     * @param string $code
     * @return string
     */
    protected function resolveClassName($code)
    {
        if (isset($this->configuration[$code])) {
            $classShortName = ucfirst($code);
            $moduleName = $this->configuration[$code]['module'];
            $class = str_replace('_', '\\', $moduleName) . '\\Test\\Fixture\\' . $classShortName;
        } else {
            $class = false;
        }

        return $class;
    }
}

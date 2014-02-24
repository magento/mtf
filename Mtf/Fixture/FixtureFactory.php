<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Fixture;

use Mtf\Factory\AbstractFactory;
use Mtf\Configuration\Reader;
use Mtf\ObjectManager;

/**
 * Factory for Fixtures
 *
 * @package Mtf\Fixture
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

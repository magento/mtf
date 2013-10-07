<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\TestCase;

use Mtf\System\Config;

/**
 * Class Functional
 *
 * Class TestCase\Functional is extended from PHPUnit_Framework_TestCase
 * and is base test case class for functional testing
 *
 * @package Mtf\TestCase
 */
class Functional extends \PHPUnit_Framework_TestCase
{
    /**
     * Constructs a test case with the given name.
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->_construct();
    }

    /**
     * Protected construct for child test cases
     */
    protected function _construct()
    {
        //
    }

    /**
     * Run the test and assert its state
     *
     * @throws \RuntimeException
     */
    protected function runTest()
    {
        try {
            $class  = new \ReflectionClass($this);
            $method = $class->getMethod($this->getName(false));
        } catch (\ReflectionException $e) {
            $this->fail($e->getMessage());
        }

        $dependencies = array();

        $parameters = $method->getParameters();
        if ($parameters) {
            $annotations = \PHPUnit_Util_Test::parseTestMethodAnnotations(get_class($this), $this->getName(false));
            $annotations = $this->parseMethodParams($annotations);

            foreach ($parameters as $parameter) {
                if (isset($annotations[$parameter->getName()]['injectable'])) {
                    /** @var $parameter \ReflectionParameter */
                    if (null !== $parameter->getClass()) {
                        $className = $parameter->getClass()->getName();

                        $config = new Config();

                        $dependencies[$parameter->getName()] = new $className($config);
                    }
                }
            }
        }

        if (!empty($dependencies)) {
            $this->setDependencyInput($dependencies);
        }

        $result = parent::runTest();

        return $result;
    }

    /**
     * Parse method parameters
     *
     * @param array $annotations
     * @return array
     */
    protected function parseMethodParams($annotations)
    {
        $result = array();

        if (isset($annotations['method']['param'])) {
            foreach ($annotations['method']['param'] as $value) {
                $value = explode(' ', $value);
                $type = array_shift($value);
                $name = array_shift($value);
                $name = str_replace('$', '', $name);

                foreach ($value as $option) {
                    $result[$name][$option] = true;
                }
                $result[$name]['type'] = $type;
            }
        }

        return $result;
    }
}

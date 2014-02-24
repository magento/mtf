<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Iterator;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;

/**
 * Class Variation
 *
 * @package Mtf\Util\Iterator
 * @api
 */
class Variation extends AbstractIterator
{
    /**
     * Column Names
     *
     * @var array
     */
    protected $header = [];

    /**
     * Parent Test Case Object
     *
     * @var Injectable
     */
    protected $testCase;

    /**
     * Constructor
     *
     * @constructor
     * @param Injectable $testCase
     */
    public function __construct(Injectable $testCase)
    {
        $this->testCase = $testCase;

        $this->data = $this->getTestCaseMethodVariations();
        $this->initFirstElement();
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
     * Return current data row
     *
     * @return array
     */
    public function current()
    {
        return $this->prepare();
    }

    /**
     * Get Test Case Method Variations
     *
     * @return array
     */
    protected function getTestCaseMethodVariations()
    {
        $data = [];
        $testFilePath = $this->testCase->getFilePath();
        if ($testFilePath) {
            $testMethodName = $this->testCase->getName(false);
            $variationFilePath = str_replace('.php', "/{$testMethodName}.csv", $testFilePath);
            if (is_readable($variationFilePath)) {
                $data = $this->readCsv($variationFilePath);
            }
        }

        return $data;
    }

    /**
     * Parse source file, extract column names information and prepare data array
     *
     * @param $variationFilePath
     * @return array
     */
    protected function readCsv($variationFilePath)
    {
        $handle = fopen($variationFilePath, 'r');

        $data = $this->header = [];

        while (($line = fgetcsv($handle, 10000, ';', '"', '\\')) !== false) {
            if ($this->header) {
                $data[] = array_combine($this->header, $line);
            } else {
                $this->header = $line;
            }
        }

        return $data;
    }

    /**
     * Convert source variation format into normal array
     *
     * @return array
     */
    protected function prepare()
    {
        $data = [];
        if ($this->current) {
            foreach ($this->current as $key => $value) {
                if (strpos($key, '/') === false) {
                    $data[$key] = $value;
                } else {
                    $this->setArrayPathValue($data, $key, $value);
                }
            }
        }
        return $data;
    }

    /**
     * Transform 'a/b/c' key reference to normal array structure
     *
     * @param array $data
     * @param $key
     * @param $value
     * @return void
     */
    protected function setArrayPathValue(array & $data, $key, $value)
    {
        $keys = explode('/', $key);
        while ($key = array_shift($keys)) {
            if (!isset($data[$key])) {
                $data[$key] = null;
                $data = & $data[$key];
            } else {
                $data = & $data[$key];
            }
        }
        $data = $value;
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\Fixture\Test;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Curl
 *
 * @package Magento\Mtf\Test\Handler\Test
 */
class Strategy implements FixtureInterface
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @constructor
     * @param $data
     * @param array $params
     * @param bool $persist
     */
    public function __construct($data, array $params = [], $persist = false)
    {
        $this->data = $data;
        $this->params = $params;
        if ($persist) {
            $this->persist();
        }
    }

    /**
     * Persists prepared data into application
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param $key [optional]
     * @return mixed
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}

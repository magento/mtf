<?php

/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util;

use Mtf\Fixture\DataFixture;
use Mtf\Repository\RepositoryInterface;

class FixtureIterator implements \Iterator
{
    /**
     * @var DataFixture
     */
    protected $_fixture;

    /**
     * @var DataFixture
     */
    protected $_current;

    /**
     * @var string
     */
    protected $_key;

    /**
     * @var RepositoryInterface
     */
    protected $_repository;

    /**
     * @param DataFixture $fixture
     */
    public function __construct(DataFixture $fixture)
    {
        $this->_fixture = $fixture;
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->_repository = $this->_fixture->getRepository();

        if ($this->_repository) {
            $data = reset($this->_repository);
            $this->_key = key($this->_repository);

            $this->_current = clone $this->_fixture;
            $this->_current->setData($data['data'], $data['config']);
        }
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->_key, $this->_repository);
    }

    /**
     * @return mixed|string
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * @return array
     */
    public function current()
    {
        return array(
            $this->_key => $this->_current
        );
    }

    /**
     * @return void
     */
    public function next()
    {
        $data = next($this->_repository);

        if (false !== $data) {
            $this->_key = key($this->_repository);
            // check config and ignore if data set is not matched to current test mode

            $this->_current = clone $this->_fixture;
            $this->_current->setData($data['data'], $data['config']);
        } else {
            $this->_key = null;
        }
    }
}

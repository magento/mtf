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

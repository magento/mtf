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

/**
 * Class AbstractIterator
 *
 * @api
 */
abstract class AbstractIterator implements \Iterator, \Countable
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Current data element
     *
     * @var mixed
     */
    protected $current;

    /**
     * Key associated with the current row data
     *
     * @var int|string
     */
    protected $key;

    /**
     * Get current element
     *
     * @return mixed
     */
    abstract public function current();

    /**
     * Check if current element is valid
     *
     * @return boolean
     */
    abstract protected function isValid();

    /**
     * Initialize Data Array
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->data);
        if (!$this->isValid()) {
            $this->next();
        }
    }

    /**
     * Seek to next valid row
     *
     * @return void
     */
    public function next()
    {
        $this->current = next($this->data);

        if ($this->current !== false) {
            if (!$this->isValid()) {
                $this->next();
            }
        } else {
            $this->key = null;
        }
    }

    /**
     * Check if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        $current = current($this->data);
        if ($current === false || $current === null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get data key of the current data element
     *
     * @return int|string
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * To make iterator countable
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Initialize first element
     *
     * @return void
     */
    protected function initFirstElement()
    {
        if ($this->data) {
            $this->current = reset($this->data);
            if (!$this->isValid()) {
                $this->next();
            }
        }
    }
}

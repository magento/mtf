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

namespace Mtf\Fixture\InjectableFixture;

use Mtf\Fixture\InjectableFixture\Replacer\Reader;

/**
 * Class replace values by path in fixture.
 */
class Replacer {
    /**
     * Environment name variable for file with replacing values.
     */
    const REPLACING_VALUES_FILE_PATH = 'replacing_values_file_path';

    /**
     * File reader for replacing values.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Replacing values.
     *
     * @var array
     */
    protected $values;

    /**
     * Temporary source data.
     *
     * @var array
     */
    protected $data;

    /**
     * @constructor
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->init();
    }

    /**
     * Load list replacing values.
     *
     * @return void
     */
    protected function init()
    {
        $filePath = getenv(self::REPLACING_VALUES_FILE_PATH);

        if ($filePath) {
            $this->values = $this->reader->read($filePath);
        } else {
            $this->values = [];
        }
    }

    /**
     * Apply replace to data.
     *
     * @param array $data
     * @return array
     */
    public function apply(array $data)
    {
        if ($this->values) {
            $this->data = $data;

            foreach ($this->values as $path => $value) {
                $this->replaceValue($path, $value);
            }
            $data = $this->data;
        }

        return $data;
    }

    /**
     * Single replace value in data.
     *
     * @param string $path
     * @param string $value
     * @return void
     */
    protected function replaceValue($path, $value)
    {
        $data = &$this->data;
        $keys = explode('/', $path);
        $isSetValue = true;

        $key = array_shift($keys);
        while (null !== $key && $isSetValue) {
            if (!isset($data[$key])) {
                $isSetValue = false;
                break;
            }

            $data = & $data[$key];
            $key = array_shift($keys);
        }

        if ($isSetValue) {
            $data = $value;
        }
    }
}

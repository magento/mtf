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

namespace Magento\Mtf\Fixture\InjectableFixture;

use Magento\Mtf\Fixture\InjectableFixture\Replacer\Reader;

/**
 * Class replace values by path in fixture.
 */
class Replacer
{
    /**
     * Environment name variable for file with replacing values.
     */
    const CREDENTIALS_FILE_PATH = 'credentials_file_path';

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
    protected $values = ['path' => [], 'replace' => []];

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
        $filePath = getenv(self::CREDENTIALS_FILE_PATH);

        if ($filePath) {
            $this->values = $this->reader->read($filePath);
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

            foreach ($this->values['path'] as $path => $value) {
                $this->applyValueByPath($path, $value);
            }
            $this->applyPlaceholders(array_merge($this->values['replace'], ['isolation' => mt_rand()]));
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
    protected function applyValueByPath($path, $value)
    {
        $data = &$this->data;
        $keys = explode('/', $path);
        $isSetValue = true;

        $key = array_shift($keys);
        while ($key !== null && $isSetValue) {
            if (!isset($data[$key])) {
                $isSetValue = false;
                break;
            }

            $data = &$data[$key];
            $key = array_shift($keys);
        }

        if ($isSetValue) {
            $data = $value;
        }
    }

    /**
     * Recursively apply placeholders to each data element
     *
     * @param array $placeholders
     * @return void
     */
    protected function applyPlaceholders(array $placeholders)
    {
        if ($placeholders) {
            $replacePairs = [];
            foreach ($placeholders as $pattern => $replacement) {
                $replacePairs['%' . $pattern . '%'] = $replacement;
            }
            $callback = function (&$value) use ($replacePairs) {
                foreach ($replacePairs as $pattern => $replacement) {
                    if (is_string($value) && strpos($value, $pattern) !== false) {
                        if (is_callable($replacement)) {
                            $param = trim($pattern, '%');
                            $replacement = $replacement($param);
                        }

                        $value = str_replace($pattern, $replacement, $value);
                    }
                }
            };
            array_walk_recursive($this->data, $callback);
        }
    }
}

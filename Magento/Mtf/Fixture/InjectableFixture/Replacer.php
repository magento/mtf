<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
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

        if (!file_exists($filePath)) {
            $filePath = realpath(MTF_BP . $filePath);
        }
        if (is_file($filePath)) {
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
                if (isset($this->data['section'][$path]['value'])) {
                    $this->data['section'][$path]['value'] = $value;
                }
            }
            $this->applyPlaceholders(array_merge($this->values['replace'], ['isolation' => mt_rand()]));
            $data = $this->data;
        }

        return $data;
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

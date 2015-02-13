<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

use Magento\Mtf\ObjectManagerInterface;

/**
 * Class Generate
 *
 * Abstract Generate
 *
 * @api
 * @abstract
 */
abstract class AbstractGenerate
{
    /**
     * Counter
     *
     * @var int
     */
    protected $cnt = 0;

    /**
     * An array of errors
     *
     * @var string[]
     */
    private $errors = [];

    /**
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Launch generation of all classes
     *
     * @return mixed
     */
    abstract public function launch();

    /**
     * Generate single class
     *
     * @param string $name
     * @return string
     */
    abstract public function generate($name);

    /**
     * Convert class name to camel-case
     *
     * @param string $class
     * @return string
     */
    protected function toCamelCase($class)
    {
        $class = str_replace('_', ' ', $class);
        $class = str_replace('\\', ' ', $class);
        $class = str_replace('/', ' ', $class);

        return str_replace(' ', '', ucwords($class));
    }

    /**
     * Prepare short information about license
     *
     * @return string
     */
    private function getLicenseNotice()
    {
        return <<<EOT
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
EOT;
    }

    /**
     * Prepare data for phpdoc attribute "copyright"
     *
     * @return string
     */
    private function getCopyright()
    {
        return 'Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)';
    }

    /**
     * Prepare data for phpdoc attribute "license"
     *
     * @return string
     */
    private function getLicenseLink()
    {
        return 'http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)';
    }

    /**
     * Get file phpdoc with license and copyright information
     *
     * @return string
     */
    protected function getFilePhpDoc()
    {
        $content = "/**\n";
        $content .= $this->getLicenseNotice() . "\n";
        $content .= " *\n";
        $content .= " * @copyright   " . $this->getCopyright() . "\n";
        $content .= " * @license     " . $this->getLicenseLink() .  "\n";
        $content .= " */\n\n";
        return $content;
    }

    /**
     * Add error message
     *
     * @param string $message
     * @return void
     */
    protected function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * Get list of occurred errors
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

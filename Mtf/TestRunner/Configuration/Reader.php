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

namespace Mtf\TestRunner\Configuration;

use Mtf\TestRunner\Configuration\FileResolver\Primary;

/**
 * Reader for test runner configuration.
 */
class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * List of name attributes for merge.
     *
     * @var array
     */
    protected $_idAttributes = [
        '/config/rule' => 'scope',
        '/config/rule/allow/namespace' => 'value',
        '/config/rule/allow/type' => 'value',
        '/config/rule/allow/module' => 'value',
        '/config/rule/deny/namespace' => 'value',
        '/config/rule/deny/type' => 'value',
        '/config/rule/deny/module' => 'value',
        '/config/rule/allow/tag' => 'value',
        '/config/rule/deny/tag' => 'value',
    ];

    /**
     * @constructor
     * @param Primary $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param ValidationState $validationState
     * @param string $fileName [optional]
     * @param array $idAttributes [optional]
     * @param string $domDocumentClass [optional]
     * @param string $defaultScope [optional]
     */
    public function __construct(
        Primary $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        ValidationState $validationState,
        $fileName = '',
        $idAttributes = [],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
    ) {
        $fileName = isset($_ENV['configuration:Mtf/TestSuite/MtfTests'])
            ? $_ENV['configuration:Mtf/TestSuite/MtfTests']
            : $fileName;

        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}

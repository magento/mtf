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

namespace Mtf\Configuration;

use Mtf\Util\XmlConverter;
use Mtf\Config\FileResolver\Module;

/**
 * Class Reader
 *
 * @internal
 */
class Reader
{
    /**
     * File Resolver
     *
     * @var Module
     */
    protected $fileResolver;

    /**
     * @var XmlConverter
     */
    protected $xmlConverter;

    /**
     * @constructor
     * @param Module $fileResolver
     * @param \Mtf\Util\XmlConverter $xmlConverter
     */
    public function __construct(Module $fileResolver, XmlConverter $xmlConverter)
    {
        $this->fileResolver = $fileResolver;
        $this->xmlConverter = $xmlConverter;
    }

    /**
     * Read entity files and merge them into one array
     *
     * @param string $entityName
     * @param string $scope
     * @return array
     */
    public function read($entityName, $scope = 'etc')
    {
        $result = [];
        $files = $this->fileResolver->get($entityName . '.xml', $scope);
        foreach ($files as $file) {
            $presetXml = simplexml_load_string($file);
            if ($presetXml instanceof \SimpleXMLElement) {
                $array = $this->xmlConverter->convert($presetXml);
                if (is_array($array)) {
                    $result = array_replace($result, $array);
                }
            }
        }

        return $result;
    }
}

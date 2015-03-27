<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\ObjectManager\Config\Mapper;

use Magento\Mtf\Config\Converter\Dom\Flat as FlatConverter;
use Magento\Mtf\Config\Dom\NodePathMatcher;
use Magento\Mtf\Config\Dom\ArrayNodeConfig;

/**
 * Parser of a DI argument node that returns its array representation with no data loss
 */
class ArgumentParser
{
    /**
     * @var FlatConverter
     */
    private $converter;

    /**
     * @var string
     */
    private $argumentNodeName;

    /**
     * @param string $argumentNodeName
     */
    public function __construct($argumentNodeName = 'argument')
    {
        $this->argumentNodeName = $argumentNodeName;
    }


    /**
     * Build and return array representation of DI argument node
     *
     * @param \DOMNode $argumentNode
     * @return array|string
     */
    public function parse(\DOMNode $argumentNode)
    {
        // Base path is specified to use more meaningful XPaths in config
        return $this->getConverter()->convert($argumentNode, $this->argumentNodeName);
    }

    /**
     * Retrieve instance of XML converter, suitable for DI argument nodes
     *
     * @return FlatConverter
     */
    protected function getConverter()
    {
        if (!$this->converter) {
            $arrayNodeConfig = new ArrayNodeConfig(
                new NodePathMatcher(),
                [$this->argumentNodeName . '(/item)+' => 'name']
            );
            $this->converter = new FlatConverter($arrayNodeConfig);
        }
        return $this->converter;
    }
}

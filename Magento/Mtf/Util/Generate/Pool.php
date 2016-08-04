<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

/**
 * List of generators
 */
class Pool
{
    /**
     * List of generators
     *
     * @var array
     */
    private $generatorPool = [];

    /**
     * @param array $pool
     */
    public function __construct(array $pool)
    {
        $this->generatorPool = $pool;
    }

    /**
     * Retrieve generator pool
     * 
     * @return array
     */
    public function getGenerators()
    {
        return $this->generatorPool;
    }
}

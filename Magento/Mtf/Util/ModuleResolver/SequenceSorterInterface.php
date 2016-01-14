<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\ModuleResolver;

/**
 * Sequence sorter interface.
 */
interface SequenceSorterInterface
{
    /**
     * Sort files according to specified sequence.
     *
     * @param array $paths
     * @return mixed
     */
    public function sort(array $paths);
}

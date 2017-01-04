<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

/**
 * Interface for launch generators
 */
interface LauncherInterface
{
    /**
     * Launch generation
     * 
     * @return mixed
     */
    public function launch();
}

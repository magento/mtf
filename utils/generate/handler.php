<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

umask(0);

$mtfRoot = dirname(dirname(__DIR__));
$mtfRoot = str_replace('\\', '/', $mtfRoot);
define('MTF_BP', $mtfRoot);

include_once $mtfRoot . '/bootstrap.php';

$path = get_include_path();
$path = rtrim($path, PATH_SEPARATOR);
$path .= PATH_SEPARATOR . MTF_BP;
$path .= PATH_SEPARATOR . MTF_BP . '/lib';
set_include_path($path);

$objectManager = \Magento\Mtf\ObjectManagerFactory::getObjectManager();
/** @var $generate \Magento\Mtf\Util\Generate\Handler */
$generate = $objectManager->get('Magento\Mtf\Util\Generate\Handler');
$generate->launch();

\Magento\Mtf\Util\Generate\GenerateResult::displayResults();

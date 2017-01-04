<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

umask(0);

$mtfRoot = dirname(__DIR__);
$mtfRoot = str_replace('\\', '/', $mtfRoot);
define('MTF_BP', $mtfRoot);

include_once $mtfRoot . '/bootstrap.php';

$path = get_include_path();
$path = rtrim($path, PATH_SEPARATOR);
$path .= PATH_SEPARATOR . MTF_BP;
$path .= PATH_SEPARATOR . MTF_BP . '/lib';
set_include_path($path);

$objectManager = \Magento\Mtf\ObjectManagerFactory::getObjectManager();

$generatorPool = $objectManager->get('Magento\Mtf\Util\Generate\Pool');
foreach ($generatorPool->getGenerators() as $generator) {
    if (!$generator instanceof \Magento\Mtf\Util\Generate\LauncherInterface) {
        throw new \InvalidArgumentException(
            'Generator ' . get_class($generator) . ' should implement LauncherInterface'
        );
    }
    $generator->launch();
}

/** @var $generate \Magento\Mtf\Util\Generate\Handler */
$generate = $objectManager->get('Magento\Mtf\Util\Generate\Handler');
$generate->launch();

\Magento\Mtf\Util\Generate\GenerateResult::displayResults();

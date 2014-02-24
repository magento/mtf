<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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

$objectManager = \Mtf\ObjectManagerFactory::getObjectManager();

/** @var $generate \Mtf\Util\Generate\Constraint */
$generate = $objectManager->get('Mtf\Util\Generate\Constraint');
$generate->launch();

/** @var $generate \Mtf\Util\Generate\Fixture */
$generate = $objectManager->get('Mtf\Util\Generate\Fixture');
$generate->launch();

/** @var $generate \Mtf\Util\Generate\Page */
$generate = $objectManager->get('Mtf\Util\Generate\Page');
$generate->launch();

/** @var $generate \Mtf\Util\Generate\Repository */
$generate = $objectManager->get('Mtf\Util\Generate\Repository');
$generate->launch();

/** @var $generate \Mtf\Util\Generate\Handler */
$generate = $objectManager->get('Mtf\Util\Generate\Handler');
$generate->launch();

\Mtf\Util\Generate\GenerateResult::displayResults();

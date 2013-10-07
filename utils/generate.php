<?php

$appRoot = dirname(dirname(dirname(dirname(__DIR__))));
$mtfRoot = dirname(dirname(__FILE__));
require_once $mtfRoot . DIRECTORY_SEPARATOR . 'bootstrap.php';

$generatorConfigFile = $mtfRoot . "/utils/config/generator_config.yml.dist";
$generatorConfig = new \Mtf\System\Config($generatorConfigFile);
$params = $generatorConfig->getConfigParam();

$params['mtf_app_root'] = $appRoot;
$params['mtf_mtf_root'] = $mtfRoot;

$params = array_merge($params, $_REQUEST);

$config = new \Mtf\Util\Config(BP, $params);

$entryPoint = new \Mtf\Util\EntryPoint($config);
$entryPoint->processRequest();

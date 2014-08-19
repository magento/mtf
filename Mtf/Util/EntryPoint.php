<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util;

use Mtf\Util\Generate\BlockFactory;
use Mtf\Util\Generate\PageFactory;
use Mtf\Util\Generate\FixtureFactory;
use Mtf\Util\Generate\RepositoryFactory;
use Mtf\Util\Generate\HandlerFactory;

/**
 * Class EntryPoint
 */
class EntryPoint
{
    /**
     * Configuration parameters
     *
     * @var array
     */
    protected $_params;

    /**
     * Initialize configuration parameters
     *
     * @param array $params
     */
    public function __construct($params)
    {
        $this->_params = $params;
    }

    /**
     * Start to generate all classes
     * @return void
     */
    public function processRequest()
    {
        try {
            $blocks = new BlockFactory($this->_params);
            $blocks->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $pages = new PageFactory($this->_params);
            $pages->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $fixtures = new FixtureFactory($this->_params);
            $fixtures->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $blocks = new RepositoryFactory($this->_params);
            $blocks->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $handlers = new HandlerFactory($this->_params);
            $handlers->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
}

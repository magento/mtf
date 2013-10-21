<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
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
class EntryPoint extends \Magento\Core\Model\AbstractEntryPoint
{
    /**
     * Start to generate all classes
     */
    protected function _processRequest()
    {
        try {
            $blocks = new BlockFactory($this->_config);
            $blocks->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $pages = new PageFactory($this->_config);
            $pages->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $fixtures = new FixtureFactory($this->_config);
            $fixtures->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $blocks = new RepositoryFactory($this->_config);
            $blocks->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        try {
            $handlers = new HandlerFactory($this->_config);
            $handlers->generate();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
}

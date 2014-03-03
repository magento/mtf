<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Autoload;

class Simple
{
    /**
     * @var object
     */
    private static $_instance;

    /**
     * @return object
     */
    public static function instance()
	{
        if (!self::$_instance) {
        	$class = __CLASS__;
            self::$_instance = new $class();
        }
        return self::$_instance; 			
	}

    /**
     * @return void
     */
    public static function register()
	{	
		spl_autoload_register(array(self::instance(), 'autoload'));
	}

    /**
     * @param string $class
     * @return void
     */
    public function autoload($class)
	{
		$classFile = str_replace(' ', '/', ucwords(str_replace('_', ' ', $class)));
        $classFile.= '.php';
        @include $classFile;
	}

}

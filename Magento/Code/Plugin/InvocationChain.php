<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Code\Plugin;

class InvocationChain
{
    /**
     * Original instance whose behavior is decorated by plugins
     *
     * @var mixed
     */
    protected $_subject;

    /**
     * Name of the method to invoke
     *
     * @var string
     */
    protected $_methodName;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * List of the plugins
     *
     * @var array
     */
    protected $_pluginList;

    /**
     * @param mixed $subject
     * @param string $methodName
     * @param \Magento\ObjectManager $objectManager
     * @param array $pluginList
     */
    public function __construct($subject, $methodName, \Magento\ObjectManager $objectManager, array $pluginList)
    {
        $this->_subject = $subject;
        $this->_methodName = $methodName;
        $this->_objectManager = $objectManager;
        $this->_pluginList = $pluginList;
    }

    /**
     * Propagate invocation through the chain
     *
     * @param array $arguments
     * @return mixed
     */
    public function proceed(array $arguments)
    {
        if (count($this->_pluginList)) {
            $aroundMethodName = 'around' . ucfirst($this->_methodName);
            return $this->_objectManager->get(array_shift($this->_pluginList))->$aroundMethodName($arguments, $this);
        }
        return call_user_func_array(array($this->_subject, $this->_methodName), $arguments);
    }
}

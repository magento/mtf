<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

class Scope implements \Magento\Config\ScopeInterface, \Magento\Config\ScopeListInterface
{
    /**
     * Default application scope
     *
     * @var string
     */
    protected $_defaultScope;

    /**
     * Current config scope
     *
     * @var string
     */
    protected $_currentScope;

    /**
     * List of all available areas
     *
     * @var \Magento\App\AreaList
     */
    protected $_areaList;

    /**
     * @param \Magento\App\AreaList $areaList
     * @param string $defaultScope
     */
    public function __construct(\Magento\App\AreaList $areaList, $defaultScope = 'primary')
    {
        $this->_defaultScope = $this->_currentScope = $defaultScope;
        $this->_areaList = $areaList;
    }

    /**
     * Get current configuration scope identifier
     *
     * @return string
     */
    public function getCurrentScope()
    {
        return $this->_currentScope;
    }

    /**
     * Set current configuration scope
     *
     * @param string $scope
     */
    public function setCurrentScope($scope)
    {
        $this->_currentScope = $scope;
    }

    /**
     * Retrieve list of available config scopes
     *
     * @return array
     */
    public function getAllScopes()
    {
        $codes = $this->_areaList->getCodes();
        array_unshift($codes, $this->_defaultScope);
        return $codes;
    }
}

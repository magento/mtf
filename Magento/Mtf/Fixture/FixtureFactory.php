<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Fixture;

/**
 * Factory for Fixtures
 *
 * @api
 */
class FixtureFactory extends \Magento\Mtf\Factory\AbstractFactory
{
    /**
     * Fixtures configuration data
     *
     * @var \Magento\Mtf\Config\DataInterface
     */
    protected $configData;

    /**
     * Generated factory entity name
     *
     * @var string
     */
    protected $factoryName = 'Fixture';

    /**
     * @constructor
     * @param \Magento\Mtf\ObjectManager $objectManager
     * @param \Magento\Mtf\Config\DataInterface $configData
     */
    public function __construct(
        \Magento\Mtf\ObjectManager $objectManager,
        \Magento\Mtf\Config\DataInterface $configData
    ) {
        parent::__construct($objectManager);
        $this->configData = $configData;
    }

    /**
     * Create fixture by its code
     *
     * @param string $code
     * @param array $arguments
     * @return FixtureInterface
     */
    public function createByCode($code, array $arguments = [])
    {
        return $this->create($this->resolveClassName($code), $arguments);
    }

    /**
     * Resolve class name
     *
     * @param string $code
     * @return string
     */
    protected function resolveClassName($code)
    {
        $config = $this->configData->get('fixture/' . $code);
        if (empty($config)) {
            return false;
        }
        if (isset($config['class'])) {
            return $config['class'];
        }
        return str_replace('_', '\\', $config['module']) . '\\Test\\Fixture\\' . ucfirst($code);
    }
}

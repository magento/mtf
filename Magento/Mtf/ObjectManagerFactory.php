<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf;

use Magento\Mtf\ObjectManager\Factory;
use Magento\Mtf\Stdlib\BooleanUtils;
use Magento\Mtf\ObjectManager as MagentoObjectManager;

/**
 * Object Manager Factory.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObjectManagerFactory
{
    /**
     * Object Manager class name.
     *
     * @var string
     */
    protected $locatorClassName = '\Magento\Mtf\ObjectManager';

    /**
     * DI Config class name.
     *
     * @var string
     */
    protected $configClassName = '\Magento\Mtf\ObjectManager\Config';

    /**
     * Create Object Manager.
     *
     * @param array $sharedInstances
     * @return ObjectManager
     */
    public function create(array $sharedInstances = [])
    {
        /** @var \Magento\Mtf\ObjectManager\Config $diConfig */
        $diConfig = new $this->configClassName();

        $factory = new Factory($diConfig);
        $argInterpreter = $this->createArgumentInterpreter(new BooleanUtils());
        $argumentMapper = new \Magento\Mtf\ObjectManager\Config\Mapper\Dom($argInterpreter);

        $autoloader = new \Magento\Mtf\Code\Generator\Autoloader(
            new \Magento\Mtf\Code\Generator(
                [
                    'page' => 'Magento\Mtf\Util\Generate\Page',
                    'repository' => 'Magento\Mtf\Util\Generate\Repository',
                    'fixture' => 'Magento\Mtf\Util\Generate\Fixture'
                ]
            )
        );
        spl_autoload_register([$autoloader, 'load']);

        $sharedInstances['Magento\Mtf\Data\Argument\InterpreterInterface'] = $argInterpreter;
        $sharedInstances['Magento\Mtf\ObjectManager\Config\Mapper\Dom'] = $argumentMapper;

        /** @var \Magento\Mtf\ObjectManager $objectManager */
        $objectManager = new $this->locatorClassName($factory, $diConfig, $sharedInstances);

        $factory->setObjectManager($objectManager);
        ObjectManager::setInstance($objectManager);

        self::configure($objectManager);

        return $objectManager;
    }

    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments.
     *
     * @param \Magento\Mtf\Stdlib\BooleanUtils $booleanUtils
     * @return \Magento\Mtf\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter(
        \Magento\Mtf\Stdlib\BooleanUtils $booleanUtils
    ) {
        $constInterpreter = new \Magento\Mtf\Data\Argument\Interpreter\Constant();
        $result = new \Magento\Mtf\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\Mtf\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\Mtf\Data\Argument\Interpreter\StringType($booleanUtils),
                'number' => new \Magento\Mtf\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\Mtf\Data\Argument\Interpreter\NullType(),
                'const' => $constInterpreter,
                'object' => new \Magento\Mtf\Data\Argument\Interpreter\ObjectType($booleanUtils),
                'init_parameter' => new \Magento\Mtf\Data\Argument\Interpreter\Argument($constInterpreter),
            ],
            \Magento\Mtf\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\Mtf\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }

    /**
     * Get MTF Object Manager instance.
     *
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        if (!$objectManager = ObjectManager::getInstance()) {
            $objectManagerFactory = new self();
            $objectManager = $objectManagerFactory->create();
        }

        return $objectManager;
    }

    /**
     * Configure Object Manager.
     * This method is static to have the ability to configure multiple instances of Object manager when needed.
     *
     * @param \Magento\Mtf\ObjectManagerInterface $objectManager
     * @return void
     */
    public static function configure(\Magento\Mtf\ObjectManagerInterface $objectManager)
    {
        $objectManager->configure(
            $objectManager->get('Magento\Mtf\ObjectManager\ConfigLoader\Primary')->load()
        );

        $objectManager->configure(
            $objectManager->get('Magento\Mtf\ObjectManager\ConfigLoader\Module')->load()
        );

        self::configureHandlerFallback($objectManager);
    }

    /**
     * Configure handler fallback.
     *
     * @param \Magento\Mtf\ObjectManagerInterface $objectManager
     * @return void
     */
    protected static function configureHandlerFallback(\Magento\Mtf\ObjectManagerInterface $objectManager)
    {
        $config = $objectManager->create('\Magento\Mtf\Config\DataInterface');
        $handlerFallback = $config->get('handler/0');
        $handlers = [];
        foreach ($handlerFallback as $type => $data) {
            $handlers[$data[0]['priority']] = $type;
        }
        krsort($handlers);
        foreach ($handlers as $handler) {
            $objectManager->configure(
                $objectManager->get('Magento\Mtf\ObjectManager\ConfigLoader\Module')->load('etc/' . $handler)
            );
        }
    }
}

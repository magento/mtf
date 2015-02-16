<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Code;

/**
 * Class Generator.
 *
 * Classes generator.
 */
class Generator
{
    /**#@+
     * Generation statuses.
     */
    const GENERATION_SUCCESS = 'success';
    const GENERATION_ERROR = 'error';
    /**#@- */

    /**
     * @var string[]
     */
    protected $generatedEntities;

    /**
     * @param array $generatedEntities
     */
    public function __construct(array $generatedEntities = [])
    {
        $this->generatedEntities = $generatedEntities;
    }

    /**
     * Get generated entities.
     *
     * @return string[]
     */
    public function getGeneratedEntities()
    {
        return $this->generatedEntities;
    }

    /**
     * Generate class.
     *
     * @param string $className
     * @return string
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function generateClass($className)
    {
        $classNameRegexp = '/\\\Test\\\([^\\\]+)(?:\\\[^\\\]+)+$/';

        if (!preg_match($classNameRegexp, $className, $matches)) {
            throw new \InvalidArgumentException('Corrupted class name: ' . $className);
        }

        $classType = lcfirst($matches[1]);

        if (!isset($this->generatedEntities[$classType])) {
            throw new \InvalidArgumentException('Unknown class type: ' . $classType);
        }

        /** @var \Magento\Mtf\Util\Generate\AbstractGenerate $generator */
        $generator = $this->createGeneratorInstance($this->generatedEntities[$classType]);

        $classFilePath = $generator->generate($className);

        if (!$classFilePath) {
            throw new \Exception(implode(' ', $generator->getErrors()));
        }

        $this->includeFile($classFilePath);

        return self::GENERATION_SUCCESS;
    }

    /**
     * @param string $fileName
     * @return void
     */
    public function includeFile($fileName)
    {
        include $fileName;
    }

    /**
     * Create entity generator.
     *
     * @param string $generatorClass
     * @return \Magento\Mtf\Util\Generate\AbstractGenerate
     */
    protected function createGeneratorInstance($generatorClass)
    {
        return \Magento\Mtf\ObjectManager::getInstance()->get($generatorClass);
    }
}

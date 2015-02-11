<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Code;

class Generator
{
    const GENERATION_SUCCESS = 'success';

    const GENERATION_ERROR = 'error';

    /**
     * @var array
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
     * Get generated entities
     *
     * @return string[]
     */
    public function getGeneratedEntities()
    {
        return $this->generatedEntities;
    }

    /**
     * Generate Class
     *
     * @param string $className
     * @return string
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function generateClass($className)
    {
        $entityType = $entityName = null;

        foreach ($this->generatedEntities as $entityType => $generatorClass) {
            $entitySuffix = '\Test\\' . ucfirst($entityType);

            if (strpos($className, $entitySuffix) !== false) {
                $entityName = substr(
                    $className, strrpos($className, $entitySuffix) + strlen($entitySuffix)
                );
                $entityName = explode("\\", $entityName);
                $entityName = end($entityName);
                break;
            }
        }

        if (!$entityName) {
            return self::GENERATION_ERROR;
        }
        if (!isset($this->generatedEntities[$entityType])) {
            throw new \InvalidArgumentException('Unknown generation entity.');
        }

        /** @var \Magento\Mtf\Util\Generate\AbstractGenerate $generator */
        $generator = $this->createGeneratorInstance($this->generatedEntities[$entityType]);

        if (!($file = $generator->generate($entityName))) {
            throw new \Exception(implode(' ', $generator->getErrors()));
        }
        $this->includeFile($file);

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
     * Create entity generator
     *
     * @param string $generatorClass
     * @return \Magento\Mtf\Util\Generate\AbstractGenerate
     */
    protected function createGeneratorInstance($generatorClass)
    {
        return \Magento\Mtf\ObjectManager::getInstance()->get($generatorClass);
    }
}

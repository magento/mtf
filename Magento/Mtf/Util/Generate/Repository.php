<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

/**
 * Repository files generator.
 */
class Repository extends AbstractGenerate
{
    /**
     * @var \Magento\Mtf\Repository\Reader\Reader
     */
    protected $configData;

    /**
     * @constructor
     * @param \Magento\Mtf\ObjectManagerInterface $objectManager
     * @param \Magento\Mtf\Repository\Reader\Reader $configData
     */
    public function __construct(
        \Magento\Mtf\ObjectManagerInterface $objectManager,
        \Magento\Mtf\Repository\Reader\Reader $configData
    ) {
        parent::__construct($objectManager);
        $this->configData = $configData;
    }

    /**
     * Launch generation of all repository classes.
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        foreach ($this->configData->read() as $name => $data) {
            $this->generateClass($name, $data);
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Repository Classes', $this->cnt);
    }

    /**
     * Generate single repository class.
     *
     * @param string $name
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function generate($name)
    {
        if (!$this->configData->read($name)) {
            throw new \InvalidArgumentException('Invalid class name: ' . $name);
        }

        return $this->generateClass(
            $name, $this->configData->read($name)
        );
    }

    /**
     * Generate repository class from sources.
     *
     * @param string $name
     * @param array $data
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass($name, array $data)
    {
        $class = explode("\\", $name);

        $className = end($class);
        $namespace = $class[0];
        $module = $class[1];
        $modulePath = MTF_BP . '/generated/' . $namespace . "/" . $module;
        $folderPath = $modulePath . '/Test/Repository';

        $relativeFilePath = str_replace($modulePath . '/', '', $folderPath);
        $ns = $namespace . '\\' . $module . '\\' . str_replace('/', '\\', $relativeFilePath);
        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= "use Magento\\Mtf\\Repository\\AbstractRepository;\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends AbstractRepository\n";
        $content .= "{\n";

        $content .= "    /**\n     * @constructor\n";
        $content .= "     * @param array \$defaultConfig\n";
        $content .= "     * @param array \$defaultData\n     */\n";
        $content .= '    public function __construct(array $defaultConfig = [], array $defaultData = [])' . "\n";
        $content .= "    {\n";
        end($data);
        $lastItemName = key($data);
        foreach ($data as $name => $item) {
            $content .= "        \$this->_data['{$name}'] = ";
            $content .= $this->generateArray('', $item, '        ');
            $content .= "        ];\n";
            $content .= $lastItemName === $name ? "" : "\n";
        }
        $content .= "    }\n";

        $content .= "}\n";

        $newFilename = $className . '.php';

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $result = @file_put_contents($folderPath . '/' . $newFilename, $content);

        if ($result === false) {
            $error = error_get_last();
            $this->addError(sprintf('Unable to generate %s class. Error: %s', $className, $error['message']));
            return false;
        }

        $this->cnt++;

        return $folderPath . '/' . $newFilename;
    }

    /**
     * Generate dataSet array.
     *
     * @param string $arrayKey
     * @param array $params
     * @param string $indent
     * @param bool $flag
     * @return string
     */
    protected function generateArray($arrayKey, array $params, $indent = '', $flag = false)
    {
        $content = $arrayKey == '' ? "[\n" : $indent . "'{$arrayKey}' => [\n";
        foreach ($params as $key => $value) {
            $content .= is_array($value)
                ? $this->generateArray($key, $value, $indent . '    ', true)
                : ($indent . "    '{$key}' => '" . $value . "',\n");
        }
        $content .= !$flag ? '' : $indent . "],\n";

        return $content;
    }
}

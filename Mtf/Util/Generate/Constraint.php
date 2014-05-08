<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate;

use Magento\Framework\ObjectManager;
use Mtf\Configuration\Reader;

/**
 * Class Constraint
 *
 * Constraint files generator
 *
 * @package Mtf\Util\Generate
 * @internal
 */
class Constraint extends AbstractGenerate
{
    /**
     * @var Reader
     */
    protected $configReader;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param Reader $configReader
     */
    public function __construct(
        ObjectManager $objectManager,
        Reader $configReader
    ) {
        parent::__construct($objectManager);

        $this->configReader = $configReader;
    }

    /**
     * Launch Constraint generator
     *
     * @return bool
     */
    public function launch()
    {
        return $this->generateClasses();
    }

    /**
     * Generate Fixtures Classes
     *
     * @return bool
     */
    public function generateClasses()
    {
        $this->cnt = 0;

        $configuration = $this->configReader->read('constraint');
        foreach ($configuration as $name => $item) {
            $this->generateClass($name, $item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Constraint Classes', $this->cnt);
    }

    /**
     * Generate constraint class from declaration
     *
     * @param string $name
     * @param array $item
     * @return void
     */
    private function generateClass($name, array $item)
    {
        $classShortName = ucfirst($name);
        $fileName = $classShortName . '.php';

        $moduleName = $item['module'];
        $ns = str_replace('_', '\\', $moduleName) . '\\Test\\Constraint';
        $path = str_replace('_', '/', $moduleName) . '/Test/Constraint';

        $folderName = MTF_TESTS_PATH . $path;

        $use[] = 'use Mtf\\Constraint\\AbstractConstraint;';

        $requiredArguments = [];
        if (isset($item['require'])) {
            foreach ($item['require'] as $argName => $requiredArgument) {
                $class = $requiredArgument['class'];
                list($argClassName) = array_reverse(explode('\\', $class));
                $requiredArguments[] = [
                    'name' => $argName,
                    'class' => $argClassName
                ];
                $use[] = "use {$class};";
            }
        }

        $useString = implode("\n", $use);

        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * {license_notice}\n";
        $content .= " *\n";
        $content .= " * @copyright   {copyright}\n";
        $content .= " * @license     {license_link}\n";
        $content .= " */\n\n";
        $content .= "namespace {$ns}; \n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Class {$classShortName}\n";
        $content .= " *\n";
        $content .= " * @package {$ns}\n";
        $content .= " */\n";
        $content .= "class {$classShortName} extends AbstractConstraint\n";
        $content .= "{\n";

        $severeness = (string)$item['severeness'];
        $content .= "    /**\n";
        $content .= "     * Constraint severeness\n";
        $content .= "     *\n";
        $content .= "     * @var string\n";
        $content .= "     */\n";
        $content .= "    protected \$severeness = '{$severeness}';\n\n";

        $requiredArgumentsArray = [];
        foreach ($requiredArguments as $argument) {
            $requiredArgumentsArray[] = $argument['class'] . ' $' . $argument['name'];
        }
        $argumentsString = implode(', ', $requiredArgumentsArray);

        $content .= "    /**\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= '    public function processAssert(' . $argumentsString . ')' . "\n";
        $content .= "    {\n";
        $content .= "        //\n";
        $content .= "    }\n\n";

        $content .= "    /**\n";
        $content .= "     * @return string\n";
        $content .= "     */\n";
        $content .= '    public function toString()' . "\n";
        $content .= "    {\n";
        $content .= "        //\n";
        $content .= "    }\n";

        $content .= "}\n";

        if (file_exists($folderName . '/' . $fileName)) {
            return;
        }

        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }

        file_put_contents($folderName . '/' . $fileName, $content);

        $this->cnt++;
    }
}

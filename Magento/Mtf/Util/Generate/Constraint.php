<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Util\Generate;

/**
 * Class Constraint
 * Constraint files generator
 *
 * @internal
 */
class Constraint extends AbstractGenerate
{
    /**
     * @var \Magento\Mtf\Config\DataInterface
     */
    protected $configData;

    /**
     * @constructor
     * @param \Magento\Mtf\ObjectManagerInterface $objectManager
     * @param \Magento\Mtf\Config\DataInterface $configData
     */
    public function __construct(
        \Magento\Mtf\ObjectManagerInterface $objectManager,
        \Magento\Mtf\Config\DataInterface $configData
    ) {
        parent::__construct($objectManager);
        $this->configData = $configData;
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
        foreach ($this->configData->get('constraint') as $name => $item) {
            $this->generateClass($name, $item);
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Constraint Classes', $this->cnt);
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

        $use[] = 'use Magento\Mtf\\Constraint\\AbstractConstraint;';

        $requiredArguments = [];
        if (isset($item['argument'])) {
            foreach ($item['argument'] as $argName => $argValue) {
                $class = $argValue['value'];
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
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Class {$classShortName}\n";
        $content .= " */\n";
        $content .= "class {$classShortName} extends AbstractConstraint\n";
        $content .= "{\n";

        $severity = (string)$item['severity'];
        $content .= "    /* tags */\n";
        $content .= "    const SEVERITY = '{$severity}';\n";
        $content .= "    /* end tags */\n\n";

        $requiredArgumentsArray = [];
        foreach ($requiredArguments as $argument) {
            $requiredArgumentsArray[] = $argument['class'] . ' $' . $argument['name'];
        }
        $argumentsString = implode(",\n        ", $requiredArgumentsArray);

        $content .= "    /**\n";
        $content .= "     * @return void\n";
        $content .= "     */\n";
        $content .= '    public function processAssert(' . "\n";
        $content .= '        ' . $argumentsString . "\n";
        $content .= "    ) {\n";
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

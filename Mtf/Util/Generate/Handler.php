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
class Handler extends AbstractGenerate
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
     * Launch Handler generator
     *
     * @return bool
     */
    public function launch()
    {
        return $this->generateHandlers();
    }

    /**
     * Generate Handlers
     *
     * @return bool
     */
    protected function generateHandlers()
    {
        $this->cnt = 0;

        $configuration = $this->configReader->read('fixture');
        foreach ($configuration as $name => $item) {
            $this->generateInterface($name, $item);
            $this->generateCurl($name, $item);
            $this->generateUi($name, $item);
        }
        \Mtf\Util\Generate\GenerateResult::addResult('Handlers', $this->cnt);
    }

    /**
     * Generate Handler Ui Class from declaration
     *
     * @param string $name
     * @param array $item
     * @return void
     */
    protected function generateUi($name, array $item)
    {
        $classShortName = ucfirst($name);
        $fileName = 'Ui.php';
        $moduleName = $item['module'];
        $path = str_replace('_', '/', $moduleName) . '/Test/Handler/' . $classShortName;
        $ns = str_replace('_', '\\', $moduleName) . '\\Test\\Handler\\' . $classShortName;
        $folderName = MTF_TESTS_PATH . $path;
        if (file_exists($folderName . '/' . $fileName)) {
            //unlink($folderName . '/' . $fileName);
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $use[] = 'use ' . $ns . '\\' . $classShortName . 'Interface;';
        $use[] = 'use Mtf\\Fixture\\FixtureInterface;';
        $use[] = 'use Mtf\\Handler\\Ui as AbstractUi;';
        $useString = implode("\n", $use);
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * {license_notice}\n";
        $content .= " *\n";
        $content .= " * @copyright   {copyright}\n";
        $content .= " * @license     {license_link}\n";
        $content .= " */\n\n";
        $content .= "namespace {$ns};\n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Class Ui\n";
        $content .= " *\n";
        $content .= " * @package {$ns}\n";
        $content .= " */\n";
        $content .= "class Ui extends AbstractUi implements {$classShortName}Interface\n";
        $content .= "{\n";
        $content .= "   public function persist(FixtureInterface \$fixture = null)\n";
        $content .= "    {\n";
        $content .= "        //\n";
        $content .= "    }\n";
        $content .= "}\n";
        file_put_contents($folderName . '/' . $fileName, $content);
        $this->cnt++;
    }

    /**
     * Generate Handler Curl Class from declaration
     *
     * @param string $name
     * @param array $item
     * @return void
     */
    protected function generateCurl($name, array $item)
    {
        $classShortName = ucfirst($name);
        $fileName = 'Curl.php';
        $moduleName = $item['module'];
        $path = str_replace('_', '/', $moduleName) . '/Test/Handler/' . $classShortName;
        $ns = str_replace('_', '\\', $moduleName) . '\\Test\\Handler\\' . $classShortName;
        $folderName = MTF_TESTS_PATH . $path;
        if (file_exists($folderName . '/' . $fileName)) {
            //unlink($folderName . '/' . $fileName);
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $use[] = 'use ' . $ns . '\\' . $classShortName . 'Interface;';
        $use[] = 'use Mtf\\Fixture\\FixtureInterface;';
        $use[] = 'use Mtf\\Handler\\Curl as AbstractCurl;';
        $useString = implode("\n", $use);
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * {license_notice}\n";
        $content .= " *\n";
        $content .= " * @copyright   {copyright}\n";
        $content .= " * @license     {license_link}\n";
        $content .= " */\n\n";
        $content .= "namespace {$ns};\n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Class Curl\n";
        $content .= " *\n";
        $content .= " * @package {$ns}\n";
        $content .= " */\n";
        $content .= "class Curl extends AbstractCurl implements {$classShortName}Interface\n";
        $content .= "{\n";
        $content .= "   public function persist(FixtureInterface \$fixture = null)\n";
        $content .= "    {\n";
        $content .= "        //\n";
        $content .= "    }\n";
        $content .= "}\n";
        file_put_contents($folderName . '/' . $fileName, $content);
        $this->cnt++;
    }

    /**
     * Generate Handler Interface from declaration
     *
     * @param string $name
     * @param array $item
     * @return void
     */
    protected function generateInterface($name, array $item)
    {
        $classShortName = ucfirst($name);
        $fileName = $classShortName . 'Interface.php';
        $moduleName = $item['module'];
        $path = str_replace('_', '/', $moduleName) . '/Test/Handler/' . $classShortName;
        $ns = str_replace('_', '\\', $moduleName) . '\\Test\\Handler\\' . $classShortName;
        $folderName = MTF_TESTS_PATH . $path;
        if (file_exists($folderName . '/' . $fileName)) {
            //unlink($folderName . '/' . $fileName);
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $use[] = 'use Mtf\\Handler\\HandlerInterface;';
        $useString = implode("\n", $use);
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * {license_notice}\n";
        $content .= " *\n";
        $content .= " * @copyright   {copyright}\n";
        $content .= " * @license     {license_link}\n";
        $content .= " */\n\n";
        $content .= "namespace {$ns};\n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Interface {$classShortName}Interface\n";
        $content .= " *\n";
        $content .= " * @package {$ns}\n";
        $content .= " */\n";
        $content .= "interface {$classShortName}Interface extends HandlerInterface\n";
        $content .= "{\n";
        $content .= "   //\n";
        $content .= "}\n";
        file_put_contents($folderName . '/' . $fileName, $content);
        $this->cnt++;
    }
}

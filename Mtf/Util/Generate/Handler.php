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

namespace Mtf\Util\Generate;

use Mtf\ObjectManagerInterface;
use Mtf\Config\DataInterface;

/**
 * Class Constraint
 * Constraint files generator
 *
 * @internal
 */
class Handler extends AbstractGenerate
{
    /**
     * @var DataInterface
     */
    protected $configData;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param DataInterface $configData
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DataInterface $configData
    ) {
        parent::__construct($objectManager);
        $this->configData = $configData;
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
        foreach ($this->configData->get() as $name => $item) {
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
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Class Ui\n";
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
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Class Curl\n";
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
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$ns};\n\n";
        $content .= $useString . "\n\n";
        $content .= "/**\n";
        $content .= " * Interface {$classShortName}Interface\n";
        $content .= " */\n";
        $content .= "interface {$classShortName}Interface extends HandlerInterface\n";
        $content .= "{\n";
        $content .= "   //\n";
        $content .= "}\n";
        file_put_contents($folderName . '/' . $fileName, $content);
        $this->cnt++;
    }
}

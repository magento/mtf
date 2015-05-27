<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Generate;

/**
 * Handler files generator.
 */
class Handler extends AbstractGenerate
{
    /**
     * Launch Handler generator.
     *
     * @return bool
     */
    public function launch()
    {
        return $this->generateHandlers();
    }

    /**
     * Generate single class.
     *
     * @param string $className
     * @return void
     */
    public function generate($className)
    {
        $item = $this->configData->get($className);
        $this->generateInterface($className, $item);
        $this->generateCurl($className, $item);
        $this->generateUi($className, $item);
    }

    /**
     * Generate Handlers.
     *
     * @return void
     */
    protected function generateHandlers()
    {
        $this->cnt = 0;
        foreach ($this->configData->get('fixture') as $name => $item) {
            $this->generateInterface($name, $item);
            $this->generateCurl($name, $item);
            $this->generateUi($name, $item);
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Handlers', $this->cnt);
    }

    /**
     * Generate Handler Ui Class from declaration.
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
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $use[] = 'use Magento\Mtf\\Fixture\\FixtureInterface;';
        $use[] = 'use Magento\Mtf\\Handler\\Ui as AbstractUi;';
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
     * Generate Handler Curl Class from declaration.
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
        $namespace = str_replace('_', '\\', $moduleName) . '\\Test\\Handler\\' . $classShortName;
        $folderName = MTF_TESTS_PATH . $path;
        if (file_exists($folderName . '/' . $fileName)) {
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $use[] = 'use Magento\Mtf\\Fixture\\FixtureInterface;';
        $use[] = 'use Magento\Mtf\\Handler\\Curl as AbstractCurl;';
        $useString = implode("\n", $use);
        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$namespace};\n\n";
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
     * Generate Handler Interface from declaration.
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
        $namespace = str_replace('_', '\\', $moduleName) . '\\Test\\Handler\\' . $classShortName;
        $folderName = MTF_TESTS_PATH . $path;
        if (file_exists($folderName . '/' . $fileName)) {
            return;
        }
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        $use[] = 'use Magento\Mtf\\Handler\\HandlerInterface;';
        $useString = implode("\n", $use);
        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$namespace};\n\n";
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

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

use Magento\Mtf\ObjectManagerInterface;
use Magento\Mtf\Config\FileResolver\Module;
use Magento\Mtf\Util\Generate\Fixture\FieldsProviderInterface;
use Magento\Mtf\Util\XmlConverter;
use Magento\Mtf\Util\ModuleResolver;

/**
 * Class Page
 * Page files generator
 *
 * @internal
 */
class Page extends AbstractGenerate
{
    /**
     * @var FieldsProviderInterface
     */
    protected $fieldsProvider;

    /**
     * File Resolver
     *
     * @var Module
     */
    protected $fileResolver;

    /**
     * @var XmlConverter
     */
    protected $xmlConverter;

    /**
     * @var ModuleResolver
     */
    protected $moduleResolver;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param Module $fileResolver
     * @param XmlConverter $xmlConverter
     * @param ModuleResolver $moduleResolver
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Module $fileResolver,
        XmlConverter $xmlConverter,
        ModuleResolver $moduleResolver = null
    ) {
        parent::__construct($objectManager);

        $this->fileResolver = $fileResolver;
        $this->xmlConverter = $xmlConverter;
        if ($moduleResolver) {
            $this->moduleResolver = $moduleResolver;
        } else {
            $this->moduleResolver = ModuleResolver::getInstance();
        }
    }

    /**
     * Launch Page generators
     * @return void
     */
    public function launch()
    {
        $this->generateXml();
        $this->generateClasses();
    }

    /**
     * Generate Pages XML
     * @return void
     */
    public function generateXml()
    {
        $this->cnt = 0;

        $pages = $this->fileResolver->get('page.xml', 'etc');
        foreach ($pages as $page) {
            $configXml = simplexml_load_string($page);
            if ($configXml instanceof \SimpleXMLElement) {
                $config = $this->xmlConverter->convert($configXml);
                $modulePath = $config['module'];
                unset($config['module']);

                foreach ($config as $class => $pageItem) {
                    $pageItem['module_path'] = $modulePath;
                    $pageItem['class'] = $class;
                    $this->generatePageXml($pageItem);
                }
            }
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Page XML Files', $this->cnt);
    }

    /**
     * Generate page XML configuration file from source definition
     *
     * @param array $item
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generatePageXml(array $item)
    {
        $className = ucfirst($item['class']);
        $area = isset($item['area']) ? ('/' . ucfirst($item['area'])) : '';
        $modulePath = str_replace('_', '/', $item['module_path']);
        $path = '/' . $modulePath . '/Test/Page' . $area;
        $newFilename = $className . '.xml';
        $newFolderName = MTF_TESTS_PATH . $path;

        if (file_exists($newFolderName . '/' . $newFilename)) {
            return;
        }

        $content = '<?xml version="1.0" ?>' . "\n";
        $content .= '<!--' . "\n";
        $content .= $this->getFilePhpDoc();
        $content .= '-->' . "\n";

        $attrModule = empty($item['module']) ? '' : ' module="' . $item['module'] . '"';
        $content .= '<page mca="' . $item['mca'] . '"' . $attrModule . ">\n";

        $blocks = $this->getExamplePageBlocks();
        foreach ($blocks as $blockName => $block) {
            $content .= '    <blocks>' . "\n";
            $content .= $this->generatePageXmlBlock($blockName, $block, '        ');
            $content .= '    </blocks>' . "\n";
        }

        $content .= "</page>\n";

        if (!is_dir($newFolderName)) {
            mkdir($newFolderName, 0777, true);
        }

        file_put_contents($newFolderName . '/' . $newFilename, $content);

        $this->cnt++;
    }

    /**
     * Generate block for page xml
     *
     * @param string $blockName
     * @param array $params
     * @param string $indent [optional]
     * @return string
     */
    protected function generatePageXmlBlock($blockName, array $params, $indent = '')
    {
        $content = $indent . '<' . $blockName .'>' . "\n";
        foreach ($params as $key => $value) {
            $content .= is_array($value)
                ? $this->generatePageXmlBlock($key, $value, $indent . '    ')
                : ($indent . "    <{$key}>{$value}</$key>\n");
        }
        $content.= $indent . '</' . $blockName .'>' . "\n";

        return $content;
    }

    /**
     * Collect all blocks for page
     *
     * @return array
     */
    protected function getExamplePageBlocks()
    {
        return [
            'testBlock' => [
                'class' => 'Magento\Mtf\Test\Block\TestBlock',
                'locator' => 'body',
                'strategy' => 'tag name',
                'renders' => [
                    'simple' => [
                        'class' => 'Magento\Mtf\Test\Block\TestBlockSimple',
                        'locator' => '#viewport',
                        'strategy' => 'css selector',
                    ]
                ]
            ]
        ];
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generate Pages Classes
     * @return void
     */
    public function generateClasses()
    {
        $this->cnt = 0;

        $pagesXml = $this->collectPagesXml();
        $pages = $this->mergePagesXml($pagesXml);

        foreach ($pages as $page) {
            $this->generatePageClass($page);
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Page Classes', $this->cnt);
    }

    /**
     * Collect all xml pages
     *
     * @return array
     */
    protected function collectPagesXml()
    {
        $items = [];

        $modules = $this->moduleResolver->getModulesPath();
        foreach ($modules as $modulePath) {
            $modulePathArray = explode('/', $modulePath);
            $module = array_pop($modulePathArray);

            if (!is_readable($modulePath . '/Test/Page')) {
                continue;
            }

            $dirIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $modulePath . '/Test/Page',
                        \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                    )
                ),
                '/.xml$/i'
            );

            foreach ($dirIterator as $fileInfo) {
                /** @var $fileInfo \SplFileInfo */
                $fileName = $fileInfo->getBasename('.xml');
                $modulePath = str_replace('\\', '/', $modulePath);
                $folderPath = str_replace('\\', '/', $fileInfo->getPath());
                $area = trim(str_replace($modulePath . '/Test/Page', '', $folderPath), '/');

                $key = ($area ? "{$area}_" : '') . $fileName;
                $items[$key][] = [
                    'file_name' => $fileName,
                    'area' => $area,
                    'real_path' => str_replace('\\', '/', $fileInfo->getRealPath()),
                    'module' => $module,
                ];
            }
        }

        return $items;
    }

    /**
     * Merge xml pages
     *
     * @param array $pages
     * @return array
     */
    protected function mergePagesXml(array $pages)
    {
        $result = [];

        foreach ($pages as $key => $page) {
            $file = reset($page);
            $pageConfig = [
                'file_name' => $file['file_name'],
                'area' => $file['area'],
            ];

            foreach ($page as $file) {
                $content = file_get_contents($file['real_path']);
                $configXml = simplexml_load_string($content);

                if ($configXml instanceof \SimpleXMLElement) {
                    $pageConfig = array_replace_recursive($pageConfig, $this->xmlConverter->convert($configXml));
                }
            }

            $result[$key] = $pageConfig;
        }

        return $result;
    }

    /**
     * Generate page classes from sources
     *
     * @param array $item
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generatePageClass(array $item)
    {
        $className = $item['file_name'];
        $module =  str_replace('_', '/', $item['module']);
        $folderPath = $module . '/Test/Page' . (empty($item['area']) ? '' : ('/' . $item['area']));
        $realFolderPath = MTF_BP . '/generated/' . $folderPath;
        $namespace = str_replace('/', '\\', $folderPath);
        $areaMtfPage = strpos($folderPath, 'Adminhtml') === false ? 'FrontendPage' : 'BackendPage';
        $mca = isset($item['mca']) ? $item['mca'] : '';
        $blocks = isset($item['blocks']) ? $item['blocks'] : [];

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$namespace};\n\n";
        $content .= "use Magento\Mtf\\Page\\{$areaMtfPage};\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends {$areaMtfPage}\n";
        $content .= "{\n";
        $content .= "    const MCA = '{$mca}';\n\n";

        $content .= "    /**\n";
        $content .= "     * Blocks' config\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$blocks = [\n";
        foreach ($blocks as $blockName => $block) {
            $content .= $this->generatePageClassBlock($blockName, $block, '        ');
        }
        $content .= "    ];\n";

        foreach ($blocks as $blockName => $block) {
            $content .= "\n    /**\n";
            $content .= "     * @return \\{$block['class']}\n";
            $content .= "     */\n";
            $content .= '    public function get' . ucfirst($blockName) . '()' . "\n";
            $content .= "    {\n";
            $content .= "        return \$this->getBlockInstance('{$blockName}');\n";
            $content .= "    }\n";
        }

        $content .= "}\n";

        $newFilename = $className . '.php';

        if (file_exists($realFolderPath . '/' . $newFilename)) {
            unlink($realFolderPath . '/' . $newFilename);
        }

        if (!is_dir($realFolderPath)) {
            mkdir($realFolderPath, 0777, true);
        }

        file_put_contents($realFolderPath . '/' . $newFilename, $content);

        $this->cnt++;
    }

    /**
     * Generate block for page class
     *
     * @param string $blockName
     * @param array $params
     * @param string $indent
     * @return string
     */
    protected function generatePageClassBlock($blockName, array $params, $indent = '')
    {
        $content = $indent . "'{$blockName}' => [\n";
        foreach ($params as $key => $value) {
            $content .= is_array($value)
                ? $this->generatePageClassBlock($key, $value, $indent . '    ')
                : ($indent . "    '{$key}' => '{$value}',\n");
        }
        $content .= $indent . "],\n";

        return $content;
    }
}

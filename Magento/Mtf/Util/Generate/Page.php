<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

/**
 * Class Page
 * Page files generator
 *
 * @internal
 */
class Page extends AbstractGenerate
{
    /**
     * @var array
     */
    protected $pages;

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
        $this->pages = $configData->get('page');
    }

    /**
     * Launch generation of all page classes
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        foreach ($this->pages as $name => $data) {
            $this->generatePageClass($name, $data);
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Page Classes', $this->cnt);
    }

    /**
     * Generate single page class
     *
     * @param string $name
     * @return string|bool
     */
    public function generate($name = null)
    {
        if (is_null($name) || !isset($this->pages[$name])) {
            $this->addError('Invalid class name: ' . $name);
            return false;
        }

        return $this->generatePageClass($name, $this->pages[$name]);
    }

    /**
     * Generate page classes from sources
     *
     * @param string $name
     * @param array $data
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generatePageClass($name, array $data)
    {
        $className = ucfirst($name);
        $module =  str_replace('_', '/', $data['module']);
        $folderPath = $module . '/Test/Page' . (empty($data['area']) ? '' : ('/' . $data['area']));
        $realFolderPath = MTF_BP . '/generated/' . $folderPath;
        $namespace = str_replace('/', '\\', $folderPath);
        $areaMtfPage = strpos($folderPath, 'Adminhtml') === false ? 'FrontendPage' : 'BackendPage';
        $mca = isset($data['mca']) ? $data['mca'] : '';
        $blocks = isset($data['block']) ? $data['block'] : [];

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$namespace};\n\n";
        $content .= "use Magento\\Mtf\\Page\\{$areaMtfPage};\n\n";
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

        if (!file_put_contents($realFolderPath . '/' . $newFilename, $content)) {
            $this->addError('Unable to generate class ' . $className);
            return false;
        }

        $this->cnt++;

        return $realFolderPath . '/' . $newFilename;
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
            if (is_array($value)) {
                $content .= $this->generatePageClassBlock($key, $value, $indent . '    ');
            } else {
                $escaped = str_replace('\'', '"', $value);
                $content .= $indent . "    '{$key}' => '{$escaped}',\n";
            }
        }
        $content .= $indent . "],\n";

        return $content;
    }
}

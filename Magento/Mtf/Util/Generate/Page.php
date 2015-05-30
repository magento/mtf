<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\Util\Generate;

/**
 * Page classes generator.
 */
class Page extends AbstractGenerate
{
    /**
     * Launch generation of all page classes.
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        foreach ($this->configData->get('page') as $name => $data) {
            $this->generateClass($name, $data);
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Page Classes', $this->cnt);
    }

    /**
     * Generate single page class.
     *
     * @param string $className
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function generate($className)
    {
        $classNameParts = explode('\\', $className);
        $classDataKey = 'page/' . end($classNameParts);

        if (!$this->configData->get($classDataKey)) {
            throw new \InvalidArgumentException('Invalid class name: ' . $className);
        }

        return $this->generateClass(
            end($classNameParts), $this->configData->get($classDataKey)
        );
    }

    /**
     * Generate page class from XML source.
     *
     * @param string $name
     * @param array $data
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass($name, array $data)
    {
        $className = ucfirst($name);
        $module =  str_replace('_', '/', $data['module']);
        $area = isset($data['area']) ? $data['area'] : '';
        $folderPath = $module . '/Test/Page' . (empty($area) ? '' : ('/' . $area));
        $class = str_replace('/', '\\', $folderPath . '/' . $className);
        $mca = isset($data['mca']) ? $data['mca'] : '';
        $areaMtfPage = $this->getParentPage($area, $mca);
        $blocks = isset($data['block']) ? $data['block'] : [];

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$this->getNamespace($class)};\n\n";
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
            $content .= "     * @return \\" . $block['class'] . "\n";
            $content .= "     */\n";
            $content .= '    public function get' . ucfirst($blockName) . '()' . "\n";
            $content .= "    {\n";
            $content .= "        return \$this->getBlockInstance('{$blockName}');\n";
            $content .= "    }\n";
        }

        $content .= "}\n";

        return $this->createClass($class, $content);
    }

    /**
     * Generate block for page class.
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

    /**
     * Determine parent page class.
     *
     * @param string $area
     * @param string $mca
     * @return string
     */
    protected function getParentPage($area, $mca)
    {
        if (strpos($area, 'Adminhtml') === false) {
            if (strpos($mca, 'http') === false) {
                $areaMtfPage = 'FrontendPage';
            } else {
                $areaMtfPage = 'ExternalPage';
            }
        } else {
            $areaMtfPage = 'BackendPage';
        }
        return $areaMtfPage;
    }
}

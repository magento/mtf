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
use Magento\Mtf\Config\DataInterface as Configuration;

/**
 * Class Page
 * Page files generator
 *
 * @internal
 */
class Page extends AbstractGenerate
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param Configuration $configuration
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Configuration $configuration
    ) {
        parent::__construct($objectManager);
        $this->configuration = $configuration;
    }

    /**
     * Launch Page generators
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        $pages = $this->configuration->get('page');

        foreach ($pages as $name => $data) {
            $this->generatePageClass($name, $data);
        }
        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Page Classes', $this->cnt);
    }

    /**
     * Generate page classes from sources
     *
     * @param string $name
     * @param array $data
     * @return void
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

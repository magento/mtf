<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate;

/**
 * Class Pages
 *
 * Pages generator
 *
 * @package Mtf\Util\Generate
 */
class Pages extends GenerateAbstract
{
    /**
     * Generate Pages
     */
    public function generate()
    {
        $filteredModules = $this->_config->getParam('modules', array());

        $items = $this->collectMca(array('admin', 'frontend'), $filteredModules);

        foreach ($items as $item) {
            $this->generatePage($item);
        }

        echo 'Total Pages generated: ' . $this->_cnt . "<br />";
    }

    /**
     * Generate page by its MCA
     *
     * @param array $mca
     */
    public function generatePage(array $mca)
    {
        $className = preg_replace('#Controller$#', '', $mca['class_name']);
        $classNameArray = explode('_', $className);

        $ns = array_shift($classNameArray) . '\\' . array_shift($classNameArray) . '\\' . 'Test\\Page';
        $className = implode('', $classNameArray) . ucfirst($mca['action_name']);

        $content = "<?php\n\n";
        $content .= "namespace {$ns}; \n\n";
        $content .= "use Mtf\\Page\\Page; \n\n";
        $content .= "class {$className} extends Page\n";
        $content .= "{\n";
        $content .= "    const MCA = '{$mca['mca']}';\n";
        $content .= "}\n";

        $newFolderName = $this->_mtfRoot . '/tests/app/' . $ns;
        $newFilename = $className . '.php';

        if (file_exists($newFolderName . '/' . $newFilename)) {
            return;
        }

        $fs = new \Magento_Io_File();
        $fs->setAllowCreateFolders(true);
        $fs->open(array('path' => $newFolderName));
        $fs->write($newFilename, $content, 'w');

        $this->_cnt++;
    }
}

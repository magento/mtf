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
 * Class Pages
 * Pages generator
 */
class Pages extends GenerateAbstract
{
    /**
     * Generate Pages
     *
     * @return void
     */
    public function generate()
    {
        $filteredModules = $this->_params['modules'];

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
     * @return void
     */
    public function generatePage(array $mca)
    {
        $className = preg_replace('#Controller$#', '', $mca['class_name']);
        $classNameArray = explode('_', $className);

        $ns = array_shift($classNameArray) . '\\' . array_shift($classNameArray) . '\\' . 'Test\\Page';
        $className = implode('', $classNameArray) . ucfirst($mca['action_name']);

        $content = "<?php\n\n";
        $content .= "namespace {$ns};\n\n";
        $content .= "use Magento\Mtf\\Page\\Page;\n\n";
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

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

use Mtf\Util\Config;

/**
 * Class GenerateAbstract
 * Generator's core
 */
class GenerateAbstract
{
    /** Using for check settings from configuration file */
    const GENERATE_BY_MODULES = 'yes';

    /**
     * Counter
     *
     * @var int
     */
    protected $_cnt = 0;

    /**
     * Application root path
     *
     * @var string
     */
    protected $_appRoot;

    /**
     * Framework root path
     *
     * @var string
     */
    protected $_mtfRoot;

    /**
     * Generation fallback
     *
     * @var array
     */
    protected $_fallback;

    /**
     * Factory content
     *
     * @var string
     */
    protected $_factoryContent;

    /**
     * Rewrites check list
     *
     * @var array
     */
    protected $_checkList = array();

    /**
     * Generator settings
     *
     * @var array
     */
    protected $_generatorSettings;

    /**
     * Configuration instance
     *
     * @var array
     */
    protected $_params;

    /**
     * Initialize required configuration parameters
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->_params = $params;

        $this->_appRoot = str_replace('\\', '/', $this->_params['mtf_app_root']);
        $this->_mtfRoot = str_replace('\\', '/', $this->_params['mtf_mtf_root']);

        $generatorConfig = $this->_params['generator_config'];
        $this->_fallback = $generatorConfig['tests_fallback'];
    }

    /**
     * Add header content
     *
     * @param string $type
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _startFactory($type)
    {
        $this->_factoryContent = "<?php\n\n";
        $this->_factoryContent .= "namespace Mtf\\{$type};\n\n";
        $this->_factoryContent .= "use Mtf\\System\\Config;\n\n";
        $this->_factoryContent .= "class {$type}Factory\n";
        $this->_factoryContent .= "{\n";

        $this->_factoryContent .= "    /**
     * Configuration
     *
     * @var Config
     */
    protected \$_configuration;

    /**
     * Constructor
     *
     * @param Config \$configuration
     */
    public function __construct(Config \$configuration)
    {
        \$this->_configuration = \$configuration;
    }\n";
    }

    /**
     * Save content to file
     *
     * @param string $type
     * @return GenerateAbstract
     * @throws \RuntimeException
     */
    protected function _endTypeFactory($type)
    {
        if (!$this->_cnt) {
            return $this;
        }

        $this->_checkAndCreateFolder($this->_mtfRoot . "/generated/Mtf/{$type}");

        $this->_factoryContent .= "}\n";

        $file = $this->_mtfRoot . "/generated/Mtf/{$type}/{$type}Factory.php";
        if (false === file_put_contents($file, $this->_factoryContent)) {
            throw new \RuntimeException("Can't write content to {$file} file");
        }
    }

    /**
     * Create directory if not exist
     *
     * @param string $folder
     * @param int $mode
     * @return bool
     * @throws \Exception
     */
    protected function _checkAndCreateFolder($folder, $mode = 0777)
    {
        if (is_dir($folder)) {
            return true;
        }
        if (!is_dir(dirname($folder))) {
            $this->_checkAndCreateFolder(dirname($folder), $mode);
        }
        if (!is_dir($folder) && !$this->_mkdir($folder, $mode)) {
            throw new \Exception("Unable to create directory '{$folder}'. Access forbidden.");
        }
        return true;
    }

    /**
     * Create directory
     *
     * @param string $dir
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    protected function _mkdir($dir, $mode = 0777, $recursive = true)
    {
        return @mkdir($dir, $mode, $recursive);
    }

    /**
     * Search collect files
     *
     * @param string $type
     * @return array
     */
    public function collectItems($type)
    {
        $items = array();
        $rewrites = array();

        ksort($this->_fallback);

        while ($fallback = array_pop($this->_fallback)) {
            $path = isset($fallback['path']) ? $fallback['path'] : '';
            $ns = isset($fallback['namespace']) ? $fallback['namespace'] : '';
            $location = $path . ($ns ? ('/' . str_replace('\\', '/', $ns)) : '');

            $pattern = $this->_getPattern($type, $location);

            $filesIterator = glob($pattern, GLOB_BRACE);

            foreach ($filesIterator as $filePath) {
                if (!is_dir($filePath)) {
                    $this->_processItem($items, $rewrites, $filePath, $location, $path);
                } else {
                    $dirIterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator(
                            $filePath,
                            \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                        )
                    );
                    foreach ($dirIterator as $info) {
                        /** @var $info \SplFileInfo */
                        $this->_processItem($items, $rewrites, $info->getRealPath(), $location, $path);
                    }
                }
            }
        }
        return $items;
    }

    /**
     * Collect pages MCA
     *
     * @param array $areas
     * @param array $filterModules [optional]
     * @return array
     */
    public function collectMca(array $areas, array $filterModules = array())
    {
        $actions = array();

        foreach ($areas as $area) {
            $routersConfig = \Mage::getConfig()->getNode($area . '/routers')->asArray();
            foreach ($routersConfig as $frontName => $routerConfig) {
                $modules = array();

                if (isset($routerConfig['args']['module'])) {
                    $modules[$routerConfig['args']['module']] = array(
                        'front_name' => $frontName
                    );
                }

                if (isset($routerConfig['args']['modules'])) {
                    foreach ($routerConfig['args']['modules'] as $moduleConfig) {
                        $classPrefix = is_array($moduleConfig) ? $moduleConfig[0] : $moduleConfig;
                        $classPrefix = explode('_', $classPrefix);
                        $moduleName = array_shift($classPrefix) . '_' . array_shift($classPrefix);

                        $modules[$moduleName] = array(
                            'front_name' => $frontName,
                            'class_prefix' => $classPrefix
                        );
                    }
                }

                foreach ($modules as $moduleName => $conf) {
                    $prefix = !empty($conf['class_prefix']) ? $conf['class_prefix'] : '';
                    $frontName = $conf['front_name'];
                    if (!empty($filterModules) && !in_array($moduleName, $filterModules)) {
                        continue;
                    }

                    list ($namespace, $module) = explode('_', $moduleName);

                    $controllersPath = $this->_appRoot . '/app/code/' . $namespace . '/' . $module . '/controllers'
                        . ($prefix ? ('/' . implode('/', $prefix)) : '');

                    if (!is_dir($controllersPath)) {
                        continue;
                    }
                    $filesIterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator(
                            $controllersPath,
                            \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
                        )
                    );

                    foreach ($filesIterator as $fileInfo) {
                        /** @var $fileInfo \SplFileInfo */
                        if (!$fileInfo->isDir()) {
                            $filename = $fileInfo->getRealPath();
                            $filename = str_replace('\\', '/', $filename);

                            list($filePath, $controllerName) = explode('/controllers/', $filename);

                            $controllerName = str_replace('Controller.php', '', $controllerName);
                            $controllerName = str_replace('/', '_', $controllerName);

                            require_once $filename;

                            $className = $moduleName . '_' . $controllerName . 'Controller';

                            $class = new \ReflectionClass($className);

                            foreach ($class->getMethods() as $method) {
                                if ('hasAction' === $method->getName()) {
                                    continue;
                                }

                                if (substr($method->getName(), -6, 6) !== 'Action') {
                                    continue;
                                }

                                if (!$method->isPublic()) {
                                    continue;
                                }

                                if ($class->getName() !== $method->class) {
                                    continue;
                                }

                                $actionName = str_replace('Action', '', $method->getName());

                                $actions[$class->getName() . '::' . $actionName] = array(
                                    'class_name' => $class->getName(),
                                    'action_name' => $actionName,
                                    'area' => $area,
                                    'mca' => $frontName . '/' . strtolower($controllerName . '/' . $actionName)
                                );
                            }

                        }
                    }
                }
            }
        }

        return $actions;
    }

    /**
     * Handling file
     *
     * @param array $items
     * @param array $rewrites
     * @param string $filename
     * @param string $location
     * @param string $path
     * @return void
     * @throws \Exception
     */
    protected function _processItem(& $items, & $rewrites, $filename, $location, $path)
    {
        $filename = str_replace('\\', '/', $filename);

        $classPath = str_replace($this->_mtfRoot . '/' . $path . '/', '', $filename);
        $classPath = str_replace('.php', '', $classPath);
        $className = str_replace('/', '\\', $classPath);

        $reflectionClass = new \ReflectionClass($className);
        if ($reflectionClass->isAbstract()) {
            return;
        }
        $annotations = \PHPUnit_Util_Test::parseTestMethodAnnotations($className);

        list($fullLocationPath, $targetClassName) = explode($location . '/', $filename);
        $targetClassName = str_replace('.php', '', $targetClassName);
        $targetClassName = str_replace('/', '\\', $targetClassName);

        if (isset($this->_checkList[$targetClassName])) {
            $annotations['class']['rewrite'][0] = $this->_checkList[$targetClassName];
            $this->_checkList[$targetClassName] = $className;
        } else {
            $this->_checkList[$targetClassName] = $className;
        }

        if (isset($annotations['class']['rewrite'])) {
            $original = $annotations['class']['rewrite'][0];

            if (isset($items[$original])) {
                if (isset($items[$original]['fallback'])) {
                    $message = "Class '{$className}' rewrites class '{$original}'.\n";
                    $prevClass = key($items[$original]['fallback']);
                    $message .= "Class '{$prevClass}' also rewrites class '$original'";
                    throw new \Exception("Multiple rewrites detected:\n" . $message);
                }

                if (isset($items[$className])) {
                    $items[$original]['fallback'][$className] = $items[$className];
                } else {
                    $items[$original]['fallback'][$className]['class'] = $className;
                }

                $rewrites[$className] = & $items[$original]['fallback'][$className];

                if (isset($items[$className])) {
                    unset($items[$className]);
                }
            } elseif (isset($rewrites[$original])) {
                if (isset($rewrites[$original]['fallback'])) {
                    $message = "Class '{$className}' rewrites class '{$original}'.\n";
                    $prevClass = key($rewrites[$original]['fallback']);
                    $message .= "Class '{$prevClass}' also rewrites class '$original'";
                    throw new \Exception("Multiple rewrites detected:\n" . $message);
                }

                if (isset($items[$className])) {
                    $rewrites[$original]['fallback'][$className] = $items[$className];
                } else {
                    $rewrites[$original]['fallback'][$className]['class'] = $className;
                }

                $rewrites[$className] = & $rewrites[$original]['fallback'][$className];

                if (isset($items[$className])) {
                    unset($items[$className]);
                }
            } else {
                $items[$original]['class'] = $original;
                if (isset($items[$className])) {
                    $items[$original]['fallback'][$className] = $items[$className];
                } else {
                    $items[$original]['fallback'][$className]['class'] = $className;
                }

                $rewrites[$className] = & $items[$original]['fallback'][$className];

                if (isset($items[$className])) {
                    unset($items[$className]);
                }
            }
        } else {
            $items[$className]['class'] = $className;
        }
    }

    /**
     * Convert class name to camel-case
     *
     * @param string $class
     * @return string
     */
    protected function _toCamelCase($class)
    {
        $class = str_replace('_', ' ', $class);
        $class = str_replace('\\', ' ', $class);
        $class = str_replace('/', ' ', $class);

        return str_replace(' ', '', ucwords($class));
    }

    /**
     * Find class depends on fallback configuration
     *
     * @param array $item
     * @return string
     */
    protected function _resolveClass(array $item)
    {
        if (isset($item['fallback'])) {
            return $this->_resolveClass(reset($item['fallback']));
        }
        return $item['class'];
    }

    /**
     * Return comment text for item
     *
     * @param array $item
     * @param string $arguments
     * @return string
     */
    protected function _buildFallbackComment(array $item, $arguments = '')
    {
        if (isset($item['fallback'])) {
            $returnComment = "\n        //return new \\" . $item['class'] . "({$arguments});";
            return $this->_buildFallbackComment(reset($item['fallback']), $arguments) . $returnComment;
        }
    }

    /**
     * Return pattern depends on configuration
     *
     * @param string $type
     * @param string $location
     * @throws \RuntimeException
     * @return string
     */
    protected function _getPattern($type, $location)
    {
        $globPath = $this->_mtfRoot . '/' . $location;
        if (isset($this->_params['generate_specified_modules'])
            && $this->_params['generate_specified_modules'] == static::GENERATE_BY_MODULES
        ) {
            $configModules = \Mtf\ObjectManager::getInstance()->get('Mtf\Config');
            $modules = $configModules->getParameter(null, $this->_params['specified_modules']);
            if (empty($modules)) {
                throw new \RuntimeException('Generator modules configuration file is empty');
            }
            $modules = implode(' ', $modules);
            $modules = str_replace(array('_', ' '), array('/', ','), $modules);
            $globPath .= '/{' . $modules . '}/' . $type . '/*';
        } else {
            $globPath .= '/*/*/Test/' . $type . '/*';
        }
        return $globPath;
    }
}

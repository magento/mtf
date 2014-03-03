<?php
/**
 * An autoloader that uses include path. Compliant with PSR-0 standard
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Autoload;

class IncludePath
{
    /**
     * Namespaces separator
     */
    const NS_SEPARATOR = '\\';

    /**
     * Find a file in include path
     *
     * @param string $class
     * @return string|bool
     */
    public static function getFile($class)
    {
        $relativePath = self::getFilePath($class);
        return stream_resolve_include_path($relativePath);
    }

    /**
     * Get relative file path for specified class
     *
     * @static
     * @param string $class
     * @return string
     */
    public static function getFilePath($class)
    {
        if (strpos($class, self::NS_SEPARATOR) !== false) {
            $class = ltrim(str_replace(self::NS_SEPARATOR, '_', $class), '_');
        }
        return str_replace('_', '/', $class) . '.php';
    }

    /**
     * Add specified path(s) to the current include_path
     *
     * @param string|array $path
     * @param bool $prepend Whether to prepend paths or to append them
     * @return void
     */
    public static function addIncludePath($path, $prepend = true)
    {
        $includePathExtra = implode(PATH_SEPARATOR, (array)$path);
        $includePath = get_include_path();
        $pathSeparator = $includePath && $includePathExtra ? PATH_SEPARATOR : '';
        if ($prepend) {
            $includePath = $includePathExtra . $pathSeparator . $includePath;
        } else {
            $includePath = $includePath . $pathSeparator . $includePathExtra;
        }
        set_include_path($includePath);
    }

    /**
     * Resolve a class file and include it
     *
     * @param string $class
     * @return void
     */
    public static function load($class)
    {
        $file = self::getFile($class);
        if ($file) {
            include $file;
        }
    }
}

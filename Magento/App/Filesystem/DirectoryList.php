<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Filesystem;

use Magento\App\Filesystem;

/**
 * Class DirectoryList
 * @package Magento\App\Filesystem
 */
class DirectoryList extends \Magento\Filesystem\DirectoryList
{
    /**
     * Directories configurations
     *
     * @var array
     */
    protected $directories = array(
        Filesystem::ROOT_DIR => array(
            'path' => ''
        ),
        Filesystem::APP_DIR => array(
            'path' => 'app'
        ),
        Filesystem::MODULES_DIR => array(
            'path' => 'app/code'
        ),
        Filesystem::CONFIG_DIR => array(
            'path' => 'app/etc'
        ),
        Filesystem::LIB_DIR => array(
            'path' => 'lib'
        ),
        Filesystem::VAR_DIR => array(
            'path'              => 'var',
            'read_only'         => false,
            'allow_create_dirs' => true,
            'permissions'       => 0777
        ),
        Filesystem::CACHE_DIR => array(
            'path'              => 'var/cache',
            'read_only'         => false,
            'allow_create_dirs' => true,
            'permissions'       => 0777
        ),
        Filesystem::LOG_DIR => array(
            'path'              => 'var/log',
            'read_only'         => false,
            'allow_create_dirs' => true,
            'permissions'       => 0777
        ),
        Filesystem::DI_DIR => array(
            'path' => 'var/di'
        ),
        Filesystem::GENERATION_DIR => array(
            'path' => 'var/generation'
        ),
        Filesystem::HTTP => array(
            'path' => ''
        ),
        Filesystem::LOCALE_DIR => array(
            'path' => ''
        ),
        Filesystem::SYS_TMP_DIR => array(
            'path'              => '',
            'read_only'         => false,
            'allow_create_dirs' => true,
            'permissions'       => 0777
        ),
        Filesystem::SESSION_DIR => array(
            'path'              => 'var/session',
            'read_only'         => false,
            'allow_create_dirs' => true,
            'permissions'       => 0777
        )
    );

    /**
     * @param string $root
     * @param array $directories
     */
    public function __construct($root, array $directories = array())
    {
        $this->directories[Filesystem::SYS_TMP_DIR]['path'] = sys_get_temp_dir();
        parent::__construct($root, $directories);
    }
}

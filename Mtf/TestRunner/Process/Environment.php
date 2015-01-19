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
namespace Mtf\TestRunner\Process;

/**
 * Class Environment
 *
 */
class Environment
{
    /**
     * @var array
     */
    private $_environmentVariables;

    /**
     * Initializes the data in the environment
     *
     * @param array $environmentVariables
     */
    public function __construct(array $environmentVariables)
    {
        $this->_environmentVariables = $environmentVariables;
    }

    /**
     * Returns the environment variables to set.
     *
     * @return array the environment variables to set
     */
    public function getEnvironmentVariables()
    {
        return $this->_environmentVariables;
    }

    /**
     * Gets the set of environments available.
     *
     * @return Environment[]
     */
    public static function getEnvironments()
    {
        $instanceSpecificKeys = [
            'app_frontend_url',
            'app_backend_url'
        ];

        $globalKeys = [
            'app_config_path',
            'server_config_path',
            'isolation_config_path',
            'handlers_config_path',
            'install_config_path',
            'testsuite_rule',
            'testsuite_rule_path',
            'app_instances',
            'events_preset',
            'log_directory',
            'module_whitelist'
        ];

        $globalEnvironmentVariables = [];
        foreach ($globalKeys as $key) {
            if (isset($_ENV[$key])) {
                $globalEnvironmentVariables[$key] = $_ENV[$key];
            }
        }

        $environments = [];
        if (isset($_ENV['app_instances'])) {
            $instancesCount = $_ENV['app_instances'];

            for ($x = 0; $x < $instancesCount; $x++) {
                $environmentVariables = [];

                foreach ($instanceSpecificKeys as $key) {
                    $instanceKey = $key . "_" . $x;
                    if (isset($_ENV[$instanceKey])) {
                        $environmentVariables[$key] = $_ENV[$instanceKey];
                    }
                }

                $mergedEnvironmentVariables = array_merge(
                    $globalEnvironmentVariables,
                    $environmentVariables
                );
                $environments[] = new Environment($mergedEnvironmentVariables);
            }
        } else {
            $environmentVariables = [];

            foreach ($instanceSpecificKeys as $key) {
                if (isset($_ENV[$key])) {
                    $environmentVariables[$key] = $_ENV[$key];
                }
            }

            $mergedEnvironmentVariables = array_merge(
                $globalEnvironmentVariables,
                $environmentVariables
            );
            $environments[] = new Environment($mergedEnvironmentVariables);
        }

        return $environments;
    }
}

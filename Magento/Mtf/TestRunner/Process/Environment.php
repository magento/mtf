<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\TestRunner\Process;

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

        $globalEnvironmentVariables = [];
        foreach ($_ENV as $key => $value) {
            if (strpos($key, '_url') === false && strpos($key, 'app_instances') === false) {
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

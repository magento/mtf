<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\TestRunner\Process;

/**
 * Class ProcessManager
 *
 */
class ProcessManager
{
    /**
     * @var ProcessManager
     */
    private static $instance;

    /**
     * @return ProcessManager
     */
    public static function factory()
    {
        if (self::$instance == null) {
            $environments = Environment::getEnvironments();

            self::$instance = new ProcessManager($environments);
        }
        return self::$instance;
    }

    /**
     * @var Process[]
     */
    protected $_processes = [];

    /**
     * @var Environment[]
     */
    protected $_environments = [];

    /**
     * @var bool
     */
    protected $isParallelRun = false;

    /**
     * 30 minutes
     *
     * @var int
     */
    protected $processRunTimeout = 1800;

    /**
     * @param Environment[] $environments
     */
    public function __construct(array $environments)
    {
        $this->_environments = $environments;
        if (count($environments) > 1) {
            $this->isParallelRun = true;
        }
    }

    /**
     * @param \PHPUnit_Framework_Test $testcase
     * @param \PHPUnit_Framework_TestResult $result
     * @param array $params
     * @return void
     */
    public function run(
        \PHPUnit_Framework_Test $testcase,
        \PHPUnit_Framework_TestResult $result,
        array $params = array()
    ) {
        $environment = $this->popEnvironment();
        $job = $this->generateTestCaseJob($testcase, $result, $params, $environment);
        $this->processJob($job, $testcase, $result, $environment);
    }

    /**
     * Iterates through the various processes and makes sure at least one
     * completes.
     *
     * @return void
     * @throws \PHPUnit_Framework_Exception
     */
    public function waitForSingleProcessToComplete()
    {
        $originalProcessCount = count($this->_processes);
        if ($originalProcessCount === 0) {
            throw new \PHPUnit_Framework_Exception('Cannot wait for process to complete. No processes!');
        }

        $endTime = time() + $this->processRunTimeout;
        while ((count($this->_processes) >= $originalProcessCount)) {
            $this->runLoop();
            if ((time() > $endTime)) {
                throw new \PHPUnit_Framework_Exception('Timeout while waiting for single process to complete!');
            }
        }
    }

    /**
     * Iterates through the various processes and makes sure they all complete
     *
     * @return void
     */
    public function waitForProcessesToComplete()
    {
        while ((count($this->_processes) > 0)) {
            $this->runLoop();
        }
    }

    /**
     * Returns if parallel runs are supported.
     *
     * @return bool
     */
    public function isParallelModeSupported()
    {
        return $this->isParallelRun;
    }

    /**
     * Iterates through all processes, reads any output and removes inactive processes
     *
     * @return void
     */
    protected function runLoop()
    {
        foreach ($this->_processes as $process) {
            $process->readStdout();
            $process->readStderr();

            if (!$process->isActive()) {
                $process->processResults();

                $process->close();

                $this->removeDoneProcess($process);
            }
        }
    }

    /**
     * Removes a process from the running list.
     *
     * @param Process $process
     * @return void
     * @throws \PHPUnit_Framework_Exception
     */
    private function removeDoneProcess($process)
    {
        $key = array_search($process, $this->_processes);
        if ($key === false) {
            throw new \PHPUnit_Framework_Exception('Undefined process is completed!');
        }
        /* Returns the environment to the list of available environments */
        $environment = $process->getEnvironment();
        $this->_environments[] = $environment;

        unset($this->_processes[$key]);
    }

    /**
     * Render job template for test case
     *
     * @param \PHPUnit_Framework_Test $testcase
     * @param \PHPUnit_Framework_TestResult $result
     * @param array $params
     * @param Environment $environment
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function generateTestCaseJob(
        \PHPUnit_Framework_Test $testcase,
        \PHPUnit_Framework_TestResult $result,
        array $params,
        Environment $environment
    ) {
        if ($result === null) {
            $result = new \PHPUnit_Framework_TestResult();
        }

        $configFile = false;
        if (isset($_SERVER['argv']['--configuration'])) {
            for ($index = 0; $index <= (count($_SERVER['argv'])); $index++) {
                if ($_SERVER['argv'][$index] == '--configuration') {
                    $configFile = $_SERVER['argv'][$index + 1];
                    break;
                }
            }
        } else {
            $phpunitPath = MTF_BP . '/phpunit.xml';
            if (file_exists($phpunitPath)) {
                $configFile = realpath($phpunitPath);
            } else {
                if (file_exists($phpunitPath . '.dist')) {
                    $configFile = realpath($phpunitPath . '.dist');
                }
            }
        }

        if (!$configFile) {
            throw new \Exception('Cannot define phpunit configuration path');
        }

        $configuration = \PHPUnit_Util_Configuration::getInstance($configFile);

        $listenerConfiguration = var_export(serialize($configuration->getListenerConfiguration()), true);

        $class = new \ReflectionClass($testcase);

        $template = new \Text_Template(
            sprintf(
                '%s%sTestCaseRun.tpl',
                __DIR__,
                DIRECTORY_SEPARATOR
            )
        );

        if ($result->getCollectCodeCoverageInformation()) {
            $coverage = 'TRUE';
        } else {
            $coverage = 'FALSE';
        }

        if (defined('MTF_BOOT_FILE')) {
            $bootstrap = var_export(MTF_BOOT_FILE, true);
        } else if (defined('PHPUNIT_COMPOSER_INSTALL')) {
            $bootstrap = var_export(PHPUNIT_COMPOSER_INSTALL, true);
        } else {
            $bootstrap = '\'\'';
        }

        if (defined('__PHPUNIT_PHAR__')) {
            $phar = var_export(__PHPUNIT_PHAR__, true);
        } else {
            $phar = '\'\'';
        }

        if ($testcase instanceof \Magento\Mtf\TestCase\Injectable) {
            $filePath = $testcase->getFilePath();
        } else {
            $filePath = false;
        }

        $data = var_export(serialize($params['data']), true);
        $includePath = var_export(get_include_path(), true);
        $env = var_export(serialize($environment->getEnvironmentVariables()), true);
        // must do these fixes because TestCaseMethod.tpl has unserialize('{data}') in it, and we can't break BC
        // the lines above used to use addcslashes() rather than var_export(), which breaks null byte escape sequences
        $data = "'." . $data . ".'";
        $includePath = "'." . $includePath . ".'";
        $env = "'." . $env . ".'";
        $listenerConfiguration = "'." . $listenerConfiguration . ".'";

        $template->setVar(
            [
                'bootstrap' => $bootstrap,
                'phar' => $phar,
                'filename' => $class->getFileName(),
                'className' => $class->getName(),
                'methodName' => $params['name'],
                'collectCodeCoverageInformation' => $coverage,
                'data' => $data,
                'dataName' => $params['dataName'],
                'include_path' => $includePath,
                'env' => $env,
                'filePath' => $filePath,
                'listenerConfiguration' => $listenerConfiguration
            ]
        );
        return $template->render();
    }

    /**
     * Process job
     *
     * @param string $job
     * @param \PHPUnit_Framework_Test $testcase
     * @param \PHPUnit_Framework_TestResult $result
     * @param Environment $environment
     * @return void
     */
    private function processJob(
        $job,
        \PHPUnit_Framework_Test $testcase,
        \PHPUnit_Framework_TestResult $result,
        Environment $environment
    ) {

        $process = new Process($job, $testcase, $result, $environment);
        $this->_processes[] = $process;

        $process->open();
    }

    /**
     * @return Environment
     * @throws \PHPUnit_Framework_Exception
     */
    private function popEnvironment()
    {
        if (count($this->_environments) == 0) {
            $this->waitForSingleProcessToComplete();
        }
        if (count($this->_environments) == 0) {
            throw new \PHPUnit_Framework_Exception('No environment even after wait!');
        }

        return array_shift($this->_environments);
    }

    /**
     * Apply app state to all environments
     *
     * @param Callable $callback
     * @param array $arguments
     * @return void
     */
    public function applyAppState($callback, array $arguments = [])
    {
        $originalFrontendUrl = $_ENV['app_frontend_url'];
        $originalBackendUrl = $_ENV['app_backend_url'];
        foreach ($this->_environments as $environment) {
            $variables = $environment->getEnvironmentVariables();
            $_ENV['app_frontend_url'] = $variables['app_frontend_url'];
            $_ENV['app_backend_url'] = $variables['app_backend_url'];
            call_user_func_array($callback, $arguments);
        }
        $_ENV['app_frontend_url'] = $originalFrontendUrl;
        $_ENV['app_backend_url'] = $originalBackendUrl;
    }
}

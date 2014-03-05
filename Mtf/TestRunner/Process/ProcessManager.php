<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\TestRunner\Process;

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
     * @param Environment[] $environments
     */
    public function __construct($environments)
    {
        $this->_environments = $environments;
    }

    /**
     * @param \PHPUnit_Framework_Test $testcase
     * @param \PHPUnit_Framework_TestResult $result
     * @param array $params
     * @return void
     */
    public function run(\PHPUnit_Framework_Test $testcase, \PHPUnit_Framework_TestResult $result, array $params = array())
    {
        $environment = $this->popEnvironment();
        $job = $this->generateTestCaseJob($testcase, $result, $params, $environment);
        $this->processJob($job, $testcase, $result, $environment);
    }

    /**
     * Iterates through the various processes and makes sure at least one
     * completes.
     *
     * @return void
     */
    public function waitForSingleProcessToComplete()
    {
        $originalProcessCount = count($this->_processes);
        if ($originalProcessCount === 0) {
            return;
        }

        /* wait for 10 minutes */
        $endTime = time() + (10 * 60);
        while ((count($this->_processes) >= $originalProcessCount) && (time() < $endTime)) {
            $this->runLoop(120);
        }
    }

    /**
     * Iterates through the various processes and makes sure they all complete
     *
     * @return void
     */
    public function waitForProcessesToComplete()
    {
        /* wait for 10 minutes */
        $endTime = time() + (10 * 60);
        while ((count($this->_processes) > 0) && (time() < $endTime)) {
            $this->runLoop(120);
        }
    }

    /**
     * Returns if parallel runs are supported.
     *
     * @return bool
     */
    public function isParallelModeSupported()
    {
        return count($this->_environments) > 1;
    }

    /**
     * Iterates through the various processes and reads any output.
     *
     * @param int $timeoutInSecs The number of seconds to wait for any action to occur in a process. Default 0 seconds.
     * @return void
     */
    protected function runLoop($timeoutInSecs = 0)
    {
        /* assemble all the streams that should be reading */
        $readStreams = [];
        foreach ($this->_processes as $p) {
            $readStreams[] = $p->getStdoutStream();
            $readStreams[] = $p->getStderrStream();
        }
        $writeStreams = NULL;
        $exceptStreams = NULL;

        if (count($readStreams) === 0) {
            return;
        }

        /* runs through stream_select */
        $num_changed_streams = stream_select($readStreams, $writeStreams, $exceptStreams, $timeoutInSecs);
        if (false === $num_changed_streams) {
            /* error here */
        } elseif ($num_changed_streams > 0) {

            $doneProcesses = [];

            /* loops through all the processes and find the one which has the stream
               ready for reading */
            foreach ($this->_processes as $p) {
                $read = false;
                if (in_array($p->getStdoutStream(), $readStreams)) {
                    /* perform a read */
                    $p->readStdout();
                    $read = true;
                }

                if (in_array($p->getStderrStream(), $readStreams)) {
                    /* perform a read */
                    $p->readStderr();
                    $read = true;
                }

                if ($read) {
                    /* figure out if the process is done */
                    if (!$p->isActive()) {
                        /* if done, process the results */
                        $p->processResults();

                        /* if done, close the streams. */
                        $p->close();

                        /* if done, return process back to queue */
                        $doneProcesses[] = $p;
                    }
                }
            }

            foreach ($doneProcesses as $p) {
                $this->removeDoneProcess($p);
            }
        }
    }

    /**
     * Removes a process from the running list.
     *
     * @param Process $process
     * @return void
     */
    private function removeDoneProcess($process) {
        $key = array_search($process, $this->_processes);
        if ($key !== false) {
            /* Returns the environment to the list of available environments */
            $environment = $process->getEnvironment();
            $this->_environments[] = $environment;

            unset($this->_processes[$key]);
        }
    }

    /**
     * Render job template for test case
     *
     * @param \PHPUnit_Framework_Test $testcase
     * @param \PHPUnit_Framework_TestResult $result
     * @param array $params
     * @param Environment $environment
     * @return string
     */
    private function generateTestCaseJob(\PHPUnit_Framework_Test $testcase, \PHPUnit_Framework_TestResult $result, array $params, Environment $environment)
    {
        if ($result === NULL) {
            $result = new PHPUnit_Framework_TestResult;
        }

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

        if ($result->isStrict()) {
            $strict = 'TRUE';
        } else {
            $strict = 'FALSE';
        }

        if (defined('PHPUNIT_COMPOSER_INSTALL')) {
            $composerAutoload = var_export(PHPUNIT_COMPOSER_INSTALL, TRUE);
        } else {
            $composerAutoload = '\'\'';
        }

        if (defined('__PHPUNIT_PHAR__')) {
            $phar = var_export(__PHPUNIT_PHAR__, TRUE);
        } else {
            $phar = '\'\'';
        }

        $data            = var_export(serialize($params['data']), TRUE);
        $includePath     = var_export(get_include_path(), TRUE);
        $env             = var_export(serialize($environment->getEnvironmentVariables()), TRUE);
        // must do these fixes because TestCaseMethod.tpl has unserialize('{data}') in it, and we can't break BC
        // the lines above used to use addcslashes() rather than var_export(), which breaks null byte escape sequences
        $data            = "'." . $data . ".'";
        $includePath     = "'." . $includePath . ".'";
        $env             = "'." . $env . ".'";

        $template->setVar(
            array(
                 'composerAutoload'               => $composerAutoload,
                 'phar'                           => $phar,
                 'filename'                       => $class->getFileName(),
                 'className'                      => $class->getName(),
                 'methodName'                     => $params['name'],
                 'collectCodeCoverageInformation' => $coverage,
                 'data'                           => $data,
                 'dataName'                       => $params['dataName'],
                 'include_path'                   => $includePath,
                 'strict'                         => $strict,
                 'env'                            => $env
            )
        );
        return $template->render();
    }

    /**
     * Process job
     *
     * @param $job
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

    private function popEnvironment()
    {
        if (count($this->_environments) == 0)  {
            $this->waitForSingleProcessToComplete();
        }

        return array_shift($this->_environments);
    }
}

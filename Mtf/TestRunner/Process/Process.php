<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Mtf\TestRunner\Process;

/**
 * Class Process
 *
 */
class Process
{
    /**
     * @var resource[]
     */
    protected $_pipes;

    /**
     * @var Process some PHP process
     */
    protected $_process;

    /**
     * @var PHPUnit_Framework_Test
     */
    protected $_test;

    /**
     * @var PHPUnit_Framework_TestResult
     */
    protected $_result;

    /**
     * @var string
     */
    protected $_job;

    /**
     * @var resource
     */
    protected $_stdout;

    /**
     * @var resource
     */
    protected $_stderr;

    /**
     * @var Environment
     */
    protected $_environment;

    public function __construct(
        $job,
        \PHPUnit_Framework_Test $test,
        \PHPUnit_Framework_TestResult $result,
        Environment $environment
    ) {
        $this->_job = $job;
        $this->_test = $test;
        $this->_result = $result;
        $this->_environment = $environment;
    }

    /**
     * @return PHPUnitUtils
     */
    protected function getPhpUnitUtils()
    {
        return new PHPUnitUtils();
    }

    /**
     * Determines if the process still is active (e.g. there is still more data to read)
     *
     * @return bool
     */
    public function isActive()
    {
        $metadata = stream_get_meta_data($this->_pipes[1]); 
        return !$metadata["eof"];
    }

    /**
     * Opens the process and starts the job.
     *
     * @return void
     */
    public function open()
    {
        $descriptor = [
            0 => [ 'pipe', 'r' ],
            1 => [ 'pipe', 'w' ],
            2 => [ 'pipe', 'w' ]
        ];

        $this->_process = proc_open(
            $this->getPhpUnitUtils()->getPhpBinary(),
            $descriptor,
            $this->_pipes
        );

        stream_set_blocking($this->_pipes[1], 0);
        stream_set_blocking($this->_pipes[2], 0);

        if (!is_resource($this->_process)) {
            throw new PHPUnit_Framework_Exception(
                'Unable to create process for process isolation.'
            );
        }

        $bytesWritten = $this->fwrite_stream($this->_pipes[0], $this->_job);
        if ($bytesWritten < strlen($this->_job)) {
            throw new PHPUnit_Framework_Exception(
                'Unable to spawn process with complete test case input'
            );
        }
        fclose($this->_pipes[0]);
    }

    /**
     * Closes the process and any associated resources
     *
     * @return void
     */
    public function close()
    {
        fclose($this->_pipes[1]);
        fclose($this->_pipes[2]);

        return proc_close($this->_process);
    }

    /**
     * Reads the standard output stream and buffers the output.
     *
     * @return void
     */
    public function readStdout()
    {
        $this->_stdout .= $this->readFromPipe($this->_pipes[1]);
    }

    /**
     * Reads the standard error stream and buffers the output.
     *
     * @return void
     */
    public function readStderr()
    {
        $this->_stderr .= $this->readFromPipe($this->_pipes[2]);
    }

    /**
     * Returns the standard output stream resource.
     *
     * @return resource
     */
    public function getStdoutStream()
    {
        return $this->_pipes[1];
    }

    /**
     * Returns the standard error stream resource.
     *
     * @return resource
     */
    public function getStderrStream()
    {
        return $this->_pipes[2];
    }

    /**
     * Returns the environment.
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Writes a string to the output pipe.
     *
     * @param resource $pipe
     * @param string $string
     * @return int Number of bytes written
     */
    protected function fwrite_stream($pipe, $string)
    {
        $fwrite = 0; // number of bytes written in an iteration
        for($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($pipe, substr($string, $written));
            if ($fwrite === false) {
                return $written;
            }
        }

        return $written;
    }

    /**
     * Reads data from a pipe.
     *
     * @param resource $pipe
     * @return string the data read
     */
    protected function readFromPipe($pipe)
    {
        $buffer = "";

        while ($bytes = fgets($pipe, 1024)) {
            $buffer .= $bytes;
        }

        return $buffer;
    }

    /**
     * Processes the standard output and standard error for the test results.
     *
     * @return void|array
     */
    public function processResults()
    {
        if ($this->_result !== NULL) {
            /* Needs to start the test here because the result itself may be shared across processes, and it
               keeps track of the current test
            */
            $this->_result->startTest($this->_test);

            $this->getPhpUnitUtils()->processChildResult($this->_test, $this->_result, $this->_stdout, $this->_stderr);
        } else {
            return array('stdout' => $this->_stdout, 'stderr' => $this->_stderr);
        }
    }
}

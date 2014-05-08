<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem;

interface WrapperInterface
{
    /**
     * @return mixed
     */
    public function dir_closedir();

    /**
     * @param string $path
     * @param array $options
     * @return mixed
     */
    public function dir_opendir($path, $options);

    /**
     * @return mixed
     */
    public function dir_readdir();

    /**
     * @return mixed
     */
    public function dir_rewinddir();

    /**
     * @param string $path
     * @param int $mode
     * @param array $options
     * @return mixed
     */
    public function mkdir($path, $mode, $options);

    /**
     * @param string $from
     * @param string $to
     * @return mixed
     */
    public function rename($from, $to);

    /**
     * @param string $path
     * @param array $options
     * @return mixed
     */
    public function rmdir($path, $options);

    /**
     * @param mixed $cast
     * @return mixed
     */
    public function stream_cast($cast);

    /**
     * @return mixed
     */
    public function stream_close();

    /**
     * @return mixed
     */
    public function stream_eof();

    /**
     * @return mixed
     */
    public function stream_flush();

    /**
     * @param mixed $operation
     * @return mixed
     */
    public function stream_lock($operation);

    /**
     * @param string $path
     * @param mixed $option
     * @param mixed $value
     * @return mixed
     */
    public function stream_metadata($path, $option, $value);

    /**
     * @param string $path
     * @param int $mode
     * @param array $options
     * @param string $openedPath
     * @return mixed
     */
    public function stream_open($path, $mode, $options, &$openedPath);

    /**
     * @param int $count
     * @return mixed
     */
    public function stream_read($count);

    /**
     * @param int $offset
     * @param int $whence
     * @return mixed
     */
    public function stream_seek($offset, $whence = SEEK_SET);

    /**
     * @param mixed $option
     * @param mixed $arg1
     * @param mixed $arg2
     * @return mixed
     */
    public function stream_set_option($option, $arg1, $arg2);

    /**
     * @return mixed
     */
    public function stream_stat();

    /**
     * @return mixed
     */
    public function stream_tell();

    /**
     * @param int $newSize
     * @return mixed
     */
    public function stream_truncate($newSize);

    /**
     * @param array $data
     * @return mixed
     */
    public function stream_write($data);

    /**
     * @param string $path
     * @return mixed
     */
    public function unlink($path);

    /**
     * @param string $path
     * @param mixed $flags
     * @return mixed
     */
    public function url_stat($path, $flags);
}

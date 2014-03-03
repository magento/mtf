<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem;

interface WrapperInterface
{
    /**
     * @return mixed
     */
    public function dir_closedir();

    /**
     * @param $path
     * @param $options
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
     * @param $path
     * @param $mode
     * @param $options
     * @return mixed
     */
    public function mkdir($path, $mode, $options);

    /**
     * @param $from
     * @param $to
     * @return mixed
     */
    public function rename($from, $to);

    /**
     * @param $path
     * @param $options
     * @return mixed
     */
    public function rmdir($path, $options);

    /**
     * @param $cast
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
     * @param $operation
     * @return mixed
     */
    public function stream_lock($operation);

    /**
     * @param $path
     * @param $option
     * @param $value
     * @return mixed
     */
    public function stream_metadata($path, $option, $value);

    /**
     * @param $path
     * @param $mode
     * @param $options
     * @param $openedPath
     * @return mixed
     */
    public function stream_open($path, $mode, $options, &$openedPath);

    /**
     * @param $count
     * @return mixed
     */
    public function stream_read($count);

    /**
     * @param $offset
     * @param int $whence
     * @return mixed
     */
    public function stream_seek($offset, $whence = SEEK_SET);

    /**
     * @param $option
     * @param $arg1
     * @param $arg2
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
     * @param $newSize
     * @return mixed
     */
    public function stream_truncate ($newSize);

    /**
     * @param $data
     * @return mixed
     */
    public function stream_write($data);

    /**
     * @param $path
     * @return mixed
     */
    public function unlink($path);

    /**
     * @param $path
     * @param $flags
     * @return mixed
     */
    public function url_stat($path, $flags);
} 

<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Config;

/**
 * Class FileIterator
 */
class FileIterator implements \Iterator, \Countable
{
    /**
     * Cache
     *
     * @var array
     */
    protected $cached = array();

    /**
     * Paths
     *
     * @var array
     */
    protected $paths = array();

    /**
     * Position
     *
     * @var int
     */
    protected $position;

    /**
     * Read directory
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Filesystem\Directory\ReadInterface $directory
     * @param array $paths
     */
    public function __construct(\Magento\Framework\Filesystem\Directory\ReadInterface $directory, array $paths)
    {
        $this->paths = $paths;
        $this->position = 0;
        $this->directoryRead = $directory;
    }

    /**
     *Rewind
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->paths);
    }

    /**
     * Current
     *
     * @return string
     */
    public function current()
    {
        if (!isset($this->cached[$this->key()])) {
            $this->cached[$this->key()] = $this->directoryRead->readFile($this->key());
        }
        return $this->cached[$this->key()];
    }

    /**
     * Key
     *
     * @return mixed
     */
    public function key()
    {
        return current($this->paths);
    }

    /**
     * Next
     *
     * @return void
     */
    public function next()
    {
        next($this->paths);
    }

    /**
     * Valid
     *
     * @return bool
     */
    public function valid()
    {
        return (bool)$this->key();
    }

    /**
     * Convert to an array
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this as $item) {
            $result[$this->key()] = $item;
        }
        return $result;
    }

    /**
     * Count
     *
     * @return int
     */
    public function count()
    {
        return count($this->paths);
    }
}

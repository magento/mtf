<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

/**
 * Class FileIterator
 */
class FileIterator implements \Iterator, \Countable
{
    /**
     * @var array
     */
    protected $cached = array();

    /**
     * @var array
     */
    protected $paths = array();

    /**
     * @var int
     */
    protected $position;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $directoryRead;

    /**
     * @param \Magento\Filesystem\Directory\ReadInterface $directory
     * @param array $paths
     */
    public function __construct(
        \Magento\Filesystem\Directory\ReadInterface $directory,
        array $paths
    ) {
        $this->paths            = $paths;
        $this->position         = 0;
        $this->directoryRead    = $directory;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->paths);
    }

    /**
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
     * @return mixed
     */
    public function key()
    {
        return current($this->paths);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->paths);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return (boolean)$this->key();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this as $item) {
            $result[$this->key()] = $item;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->paths);
    }
}

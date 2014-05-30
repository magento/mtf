<?php
namespace Mtf\System\Event;

use Mtf\System\Event\Config\Reader;

class Config
{
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getEventConfigPath()
    {
        $array = $this->reader->read('eventTags');
//        return $array....
    }

}


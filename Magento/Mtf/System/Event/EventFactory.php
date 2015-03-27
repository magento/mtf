<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Event;

use Magento\Mtf\ObjectManager;

/**
 * Factory for creating Event classes
 */
class EventFactory
{
    /**
     * ObjectManager
     *
     * @var \Magento\Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Event class object
     *
     * @param array $tags
     * @param array $subjects
     * @return \Magento\Mtf\System\Event\Event
     */
    public function create(array $tags, array $subjects)
    {
        return $this->objectManager->create(
            'Magento\Mtf\System\Event\Event',
            [
                'tags' => $tags,
                'subjects' => $subjects
            ]
        );
    }
}

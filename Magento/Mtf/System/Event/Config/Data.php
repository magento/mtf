<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Mtf\System\Event\Config;

/**
 * Class for dealing wth events.xml config file and adoption it as Config to an application
 */
class Data extends \Magento\Mtf\Config\Data
{
    /**
     * Preset name for observers configuration
     *
     * @var string
     */
    protected $presetName;

    /**
     * Parsed presets
     *
     * @var array
     */
    protected $parsedPresets;

    /**
     * Constructor
     *
     * @param \Magento\Mtf\Config\ReaderInterface $reader
     */
    public function __construct(
        \Magento\Mtf\Config\ReaderInterface $reader
    ) {
        parent::__construct($reader);

        $this->presetName = isset($_ENV['events_preset'])
            ? $_ENV['events_preset']
            : 'default';

        $this->data['observers'] = $this->getObservers();
    }

    /**
     * Returns observers
     *
     * @return array
     */
    protected function getObservers()
    {
        if (!isset($this->presetName)) {
            return [];
        }
        $metadata = $this->get('config');
        return $this->getPresetObservers($metadata[0], $this->presetName);
    }

    /**
     * Get observers for preset
     *
     * @param array $metadata
     * @param string $name
     * @throws \Exception
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getPresetObservers($metadata, $name)
    {
        $extendedObservers = $observers = [];
        foreach ($metadata['preset'] as $preset) {
            if ($preset['name'] == $name && isset($preset['observer'])) {
                $this->parsedPresets[] = $preset['name'];
                if (isset($preset['extends'])) {
                    if (!in_array($preset['extends'], $this->parsedPresets)) {
                        $extendedObservers = $this->getPresetObservers($metadata, $preset['extends']);
                    } else {
                        throw(
                            new \Exception(
                                sprintf(
                                    'Preset "%s" extends preset "%s" and vice versa. Please avoid recursion',
                                    $preset['name'],
                                    $preset['extends']
                                )
                            )
                        );
                    }
                }
                foreach ($preset['observer'] as $observer) {
                    foreach ($observer['tag'] as $tag) {
                        if (!isset($extendedObservers[$observer['class']])
                            || !in_array($tag['name'], $extendedObservers[$observer['class']])
                        ) {
                            $observers[$observer['class']][] = $tag['name'];
                        }
                    }
                }
                break;
            }
        }
        return array_merge_recursive($extendedObservers, $observers);
    }
}

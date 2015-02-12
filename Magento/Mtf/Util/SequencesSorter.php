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
namespace Magento\Mtf\Util;

/**
 * Sorts array elements depends on next, prev keys
 *
 * Class SequencesSorter
 */
class SequencesSorter
{

    /**
     * Sorts elements using 'next' and 'prev' keys.
     *
     * @param array $elements
     * @param $firstElement
     * @throws \Exception
     * @return array
     */
    public function sort(array $elements, $firstElement)
    {
        $result = $this->prepareSequence($elements, $firstElement);
        $result['first'] = $firstElement;
        $elements = array_diff_key($elements, $result);
        $result = empty($elements) ? $result : $this->interposeElements($elements, $result);

        return $result;
    }

    /**
     * Prepare elements by existed sequence.
     *
     * @param array $elements
     * @param $key
     * @param array $result
     * @return array
     */
    protected function prepareSequence(array $elements, $key, array $result = [])
    {
        if (isset($elements[$key])) {
            $result[$key] = $elements[$key];
        }
        if (!isset($elements[$key]) || empty($elements[$key]['next'])) {
            return $result;
        }
        return $this->prepareSequence($elements, $elements[$key]['next'], $result);
    }

    /**
     * Put elements from other modules into base elements.
     *
     * @param array $additionalElements
     * @param array $baseElements
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function interposeElements(array $additionalElements, array $baseElements)
    {
        $key = key($additionalElements);
        $chunkOfElements = $this->prepareSequence($additionalElements, $key);
        $firstElementKey = key($chunkOfElements);
        end($chunkOfElements);
        $lastElementKey = key($chunkOfElements);

        if (isset($chunkOfElements[$firstElementKey]['prev'])) {
            $this->checkElementExistence($baseElements, $chunkOfElements[$firstElementKey]['prev']);
            $prevElementKey = $chunkOfElements[$firstElementKey]['prev'];
            $chunkOfElements[$lastElementKey]['next'] = !empty($baseElements[$prevElementKey]['next'])
                ? $baseElements[$prevElementKey]['next']
                : null;
            $result[$prevElementKey]['next'] = $firstElementKey;
        }
        if (isset($chunkOfElements[$lastElementKey]['next'])) {
            $this->checkElementExistence($baseElements, $chunkOfElements[$lastElementKey]['next']);
            if ($chunkOfElements[$lastElementKey]['next'] === $baseElements['first']) {
                $baseElements['first'] = $firstElementKey;
            } else {
                $prevElement = $this->getElementWithSameNext($baseElements, $chunkOfElements[$lastElementKey]['next']);
                if ($prevElement !== null) {
                    $prevElementKey = key($prevElement);
                    $chunkOfElements[$lastElementKey]['next'] = $baseElements[$prevElementKey]['next'];
                    $baseElements[$prevElementKey]['next'] = $firstElementKey;
                }
            }
        }
        $baseElements = array_merge($baseElements, $chunkOfElements);

        $additionalElements = array_diff_key($additionalElements, $chunkOfElements);
        if (empty($additionalElements)) {
            return $baseElements;
        }
        return $this->interposeElements($additionalElements, $baseElements);
    }

    /**
     * Check element existence in elements array.
     *
     * @param array $elements
     * @param string $key
     * @return void
     * @throws \Exception
     */
    protected function checkElementExistence(array $elements, $key)
    {
        if (!isset($elements[$key])) {
            throw new \Exception("Element with such key '" . $key . "' doesn't exists.\n");
        }
    }

    /**
     * Returns element with same next.
     *
     * @param array $elements
     * @param string $key
     * @return array|null
     */
    protected function getElementWithSameNext(array $elements, $key)
    {
        foreach ($elements as $key => $value) {
            if (isset($value['next']) && $value['next'] === $key) {
                return [$key => $value];
            }
        }

        return null;
    }
}

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

namespace Mtf\Util\Generate;

/**
 * Class GenerateResult
 *
 * @api
 * @abstract
 */
class GenerateResult
{
    /**
     * Counter
     *
     * @var int
     */
    protected static $cnt = 0;

    /**
     * @var array
     */
    protected static $results = [];

    /**
     * @param mixed $item
     * @param int $count
     * @param int $time
     * @return void
     */
    public static function addResult($item, $count, $time = 0)
    {
        self::$results[] = [
            'item' => $item,
            'count' => $count,
            'time' => $time
        ];
    }

    /**
     * @return array
     */
    public static function getResults()
    {
        return self::$results;
    }

    /**
     * @return void
     */
    public static function displayResults()
    {
        global $argv;

        $results = self::getResults();
        if (isset($argv)) {
            $lengths = [
                'item' => strlen('Item'),
                'count' => strlen('Count'),
                'time' => strlen('Time')
            ];
            foreach ($results as $result) {
                $lengths['item'] = max($lengths['item'], strlen($result['item']));
                $lengths['count'] = max($lengths['count'], strlen($result['count']));
                $lengths['time'] = max($lengths['time'], strlen($result['time']));
            }
            echo "|| " . self::prepareForShell('Item', $lengths['item']);
            echo " || " . self::prepareForShell('Count', $lengths['count']);
            echo " || " . self::prepareForShell('Time', $lengths['time']) . " ||\n";
            foreach ($results as $result) {
                echo "|| " . self::prepareForShell($result['item'], $lengths['item']);
                echo " || " . self::prepareForShell($result['count'], $lengths['count']);
                echo " || " . self::prepareForShell($result['time'], $lengths['time']) . " ||\n";
            }
            echo "\n";
        } else {
            echo '<table border="1" cellpadding="5"><thead><th>Item</th><th>Count</th><th>Time</th></thead><tbody>';
            foreach ($results as $result) {
                echo "<tr><td>{$result['item']}</td><td>{$result['count']}</td><td>{$result['time']}</td></tr>";
            }
            echo "</tbody></table>";
        }
    }

    /**
     * @param string $string
     * @param int $length
     * @return string
     */
    protected static function prepareForShell($string, $length)
    {
        $suffixLength = $length - strlen($string);
        $suffix = '';
        for ($i = 0; $suffixLength > $i; $i++) {
            $suffix .= ' ';
        }
        return $string . $suffix;
    }
}

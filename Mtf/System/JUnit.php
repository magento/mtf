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
namespace Mtf\System;

/**
 * A TestListener that generates a logfile of the test execution in XML markup.
 */
class JUnit extends \PHPUnit_Util_Log_JUnit
{
    /**
     * Constructor.
     *
     * @param mixed $out
     * @param boolean $logIncompleteSkipped
     */
    public function __construct($out = null, $logIncompleteSkipped = false)
    {
        if (is_null($out)) {
            $out = $_ENV['basedir'] . '/' . $_ENV['report_file_name'];
        }
        parent::__construct($out, $logIncompleteSkipped);
    }

    /**
     * A test ended.
     *
     * @param \PHPUnit_Framework_Test $test
     * @param float $time
     * @return void
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        if (!$test instanceof \PHPUnit_Framework_Warning) {
            if ($this->attachCurrentTestCase) {
                if ($test instanceof \PHPUnit_Framework_TestCase) {
                    $numAssertions = $test->getNumAssertions();
                    $this->testSuiteAssertions[$this->testSuiteLevel] += $numAssertions;

                    $this->currentTestCase->setAttribute('assertions', $numAssertions);
                }

                $this->currentTestCase->setAttribute(
                    'time',
                    sprintf('%F', $time)
                );

                $class = new \ReflectionClass($test);

                $xpath = new \DOMXPath($this->document);
                $query = '//testsuite[@name="' . $class->name . '"]';
                $entries = $xpath->query($query);

                $entries->item(0)->appendChild($this->currentTestCase);

                $entries->item(0)->setAttribute("tests", $entries->item(0)->childNodes->length);

                $errors = $xpath->query('//testsuite[@name="' . $class->name . '"]//error')->length;
                $entries->item(0)->setAttribute("errors", $errors);

                $errors = $xpath->query('//testsuite[@name="' . $class->name . '"]//failure')->length;
                $entries->item(0)->setAttribute("failures", $errors);

                $entries->item(0)->setAttribute("time", $time);

                if (method_exists($test, 'hasOutput') && $test->hasOutput()) {
                    $systemOut = $this->document->createElement('system-out');
                    $systemOut->appendChild(
                        $this->document->createTextNode($test->getActualOutput())
                    );
                    $this->currentTestCase->appendChild($systemOut);
                }
            }
        }

        $this->attachCurrentTestCase = true;
        $this->currentTestCase = null;
    }
}

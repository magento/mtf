<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\System;

/**
 * A TestListener that generates a logfile of the test execution in XML markup.
 */
class JUnit extends \PHPUnit_Util_Log_JUnit
{
    /**
     * @constructor
     * @param mixed $out [optional]
     * @param boolean $logIncompleteSkipped [optional]
     */
    public function __construct($out = null, $logIncompleteSkipped = false)
    {
        if (is_null($out) && isset($_ENV['basedir']) && isset($_ENV['report_file_name'])) {
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
        $class = new \ReflectionClass($test);

        $xpath = new \DOMXPath($this->document);

        $queryForRemoveEmptyTestSuite = '//testsuite/testsuite/testsuite/testsuite';
        $entriesForRemoveEmptyTestSuite = $xpath->query($queryForRemoveEmptyTestSuite)->item(0);
        if ($entriesForRemoveEmptyTestSuite) {
            $entriesForRemoveEmptyTestSuite->parentNode->removeChild($entriesForRemoveEmptyTestSuite);
        }

        if (!$test instanceof \PHPUnit_Framework_Warning && !$test->getIsIncomplete()) {
            if ($test instanceof \PHPUnit_Framework_TestCase) {
                $numAssertions = $test->getNumAssertions();
                $this->testSuiteAssertions[$this->testSuiteLevel] += $numAssertions;

                $this->currentTestCase->setAttribute('assertions', $numAssertions);
            }

            $this->currentTestCase->setAttribute('time', sprintf('%F', $time));

            $query = '//testsuite[@name="' . $class->name . '"]';
            $entries = $xpath->query($query);

            $entries->item(0)->appendChild($this->currentTestCase);

            $entries->item(0)->setAttribute("tests", $entries->item(0)->childNodes->length);

            $errors = $xpath->query('//testsuite[@name="' . $class->name . '"]//error')->length;
            $entries->item(0)->setAttribute("errors", $errors);

            $errors = $xpath->query('//testsuite[@name="' . $class->name . '"]//failure')->length;
            $entries->item(0)->setAttribute("failures", $errors);

            $entries->item(0)->setAttribute("time", $entries->item(0)->getAttribute("time") + $time);

            if (method_exists($test, 'hasOutput') && $test->hasOutput()) {
                $systemOut = $this->document->createElement('system-out');
                $systemOut->appendChild(
                    $this->document->createTextNode($test->getActualOutput())
                );
                $this->currentTestCase->appendChild($systemOut);
            }
        }

        $this->attachCurrentTestCase = true;
        $this->currentTestCase = null;
    }

    /**
     * Remove empty test suite and returns the XML as a string.
     *
     * @return string
     */
    public function getXML()
    {
        $xpath = new \DOMXPath($this->document);
        $queryTestSuite = '//testsuite/testsuite/testsuite';
        $entriesTestSuite = $xpath->query($queryTestSuite);
        $countRow = $entriesTestSuite->length;
        for ($i = 0; $i < $countRow; $i++) {
            $testSuite = $entriesTestSuite->item($i);
            $childNodes = $testSuite->childNodes;
            if ($childNodes->length == 0) {
                $testSuite->parentNode->removeChild($testSuite);
            }
        }

        return parent::getXML();
    }
}

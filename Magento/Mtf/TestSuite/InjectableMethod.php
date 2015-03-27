<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\TestSuite;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\System\Event\EventManager;

/**
 * Class InjectableMethod
 *
 * @api
 */
class InjectableMethod extends InjectableTestCase
{
    /**
     * @constructor
     * @param string $class
     * @param string $name
     * @param string $path
     */
    public function __construct($class = '', $name = '', $path = '')
    {
        $this->initObjectManager();
        if (!$class || !class_exists($class, false)) {
            $this->addTest(
                self::warning(
                    sprintf('Test Case Class is not valid or empty: "%s"', $class)
                )
            );
            return;
        }
        if (!$name) {
            $this->addTest(
                self::warning(
                    sprintf('Test Method Should be set for InjectableMethod class. Test Case Class: %s', $class)
                )
            );
            return;
        }
        $this->setName($name);

        $arguments = [
            'class' => $class,
            'path' => $path,
            'name' => $name
        ];

        $theClass = new \ReflectionClass($class);
        $method = $theClass->getMethod($name);
        if (!$this->isPublicTestMethod($method)) {
            return;
        }

        $methodName = $method->getName();
        $test = self::createTest($theClass, $methodName, $arguments);
        if ($test instanceof \PHPUnit_Framework_TestCase
            || $test instanceof InjectableMethod
        ) {
            $test->setDependencies(
                \PHPUnit_Util_Test::getDependencies($class, $methodName)
            );
        }
        $this->addTest($test, \PHPUnit_Util_Test::getGroups($class, $methodName));

        $this->testCase = true;
    }

    /**
     * Custom implementation of native PHPUnit_Framework_TestSuite::createTest method
     *  - Test Case class should be instantiated with MTF Object Manager
     *  - Data Provider Test Suite should be instantiated with MTF Object Manager
     *
     * @param \ReflectionClass $theClass
     * @param string $name
     * @param array $arguments
     * @return \PHPUnit_Framework_Test
     * @throws \PHPUnit_Framework_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function createTest(\ReflectionClass $theClass, $name, array $arguments = [])
    {
        $objectManager = \Magento\Mtf\ObjectManager::getInstance();

        $class = $theClass->getName();

        $arguments['name'] = $name;

        $backupSettings = \PHPUnit_Util_Test::getBackupSettings($class, $name);
        $preserveGlobalState = \PHPUnit_Util_Test::getPreserveGlobalStateSettings($class, $name);
        $runTestInSeparateProcess = \PHPUnit_Util_Test::getProcessIsolationSettings($class, $name);

        try {
            $data = \PHPUnit_Util_Test::getProvidedData($class, $name);
        } catch (\Exception $e) {
            /** @var EventManager $eventManager */
            $eventManager = $objectManager->get('Magento\Mtf\System\Event\Event');
            $eventManager->dispatchEvent(['exception'], [$e]);

            $message = sprintf(
                'The data provider specified for %s::%s is invalid.',
                $class,
                $name
            );
            $_message = $e->getMessage();
            if (!empty($_message)) {
                $message .= "\n" . $_message;
            }

            $data = new \PHPUnit_Framework_Warning($message);
        }

        // Test method with @dataProvider.
        if (isset($data)) {
            $test = $objectManager->create(
                'Magento\Mtf\TestSuite\InjectableDataProvider',
                [
                    'class' => $class . '::' . $name
                ]
            );
            if (empty($data)) {
                $data = new \PHPUnit_Framework_Warning(
                    sprintf(
                        'No tests found in suite "%s".',
                        $test->getName()
                    )
                );
            }

            $groups = \PHPUnit_Util_Test::getGroups($class, $name);

            if ($data instanceof \PHPUnit_Framework_Warning) {
                $test->addTest($data, $groups);
            } else {
                foreach ($data as $_dataName => $_data) {
                    $_arguments = $arguments;
                    $_arguments['data'] = $_data;
                    $_arguments['dataName'] = $_dataName;
                    $_test = $objectManager->create($class, $_arguments);

                    if ($runTestInSeparateProcess) {
                        $_test->setRunTestInSeparateProcess(true);

                        if ($preserveGlobalState !== null) {
                            $_test->setPreserveGlobalState($preserveGlobalState);
                        }
                    }

                    if ($backupSettings['backupGlobals'] !== null) {
                        $_test->setBackupGlobals(
                            $backupSettings['backupGlobals']
                        );
                    }

                    if ($backupSettings['backupStaticAttributes'] !== null) {
                        $_test->setBackupStaticAttributes(
                            $backupSettings['backupStaticAttributes']
                        );
                    }

                    $test->addTest($_test, $groups);
                }
            }
        } else {
            $test = $objectManager->create($class, $arguments);
        }

        if (!isset($test)) {
            throw new \PHPUnit_Framework_Exception('No valid test provided.');
        }

        if ($test instanceof \PHPUnit_Framework_TestCase) {
            $test->setName($name);

            if ($runTestInSeparateProcess) {
                $test->setRunTestInSeparateProcess(true);

                if ($preserveGlobalState !== null) {
                    $test->setPreserveGlobalState($preserveGlobalState);
                }
            }

            if ($backupSettings['backupGlobals'] !== null) {
                $test->setBackupGlobals($backupSettings['backupGlobals']);
            }

            if ($backupSettings['backupStaticAttributes'] !== null) {
                $test->setBackupStaticAttributes(
                    $backupSettings['backupStaticAttributes']
                );
            }
        }

        return $test;
    }
}

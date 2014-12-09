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

namespace Mtf\Util\CrossModuleReference;

use Mtf\Constraint\ConstraintFactory;

/**
 * Class Constraint handles cross module reference of constraint
 */
class Constraint extends Common implements CheckerInterface
{
    /**
     * @var \Mtf\Constraint\ConstraintFactory
     */
    protected $constraintFactory;

    /**
     * @var array
     */
    protected $constraintConfig = null;

    /**
     * @var array
     */
    protected $constraintsByModule = null;

    /**
     * @var array
     */
    protected $constraintToTestCasesMap = null;

    /**
     * @constructor
     * @param ConstraintFactory $constraintFactory
     */
    public function __construct(
        ConstraintFactory $constraintFactory
    ) {
        $this->constraintFactory = $constraintFactory;
    }

    /**
     * Return a list of testcases that uses constraint defined in specified module
     *
     * @param string $moduleName
     * @return array
     */
    public function getCrossModuleReference($moduleName)
    {
        $affectedTestCases = [];
        if (!isset($this->constraintConfig)) {
            $this->initConstraintConfig();
        }

        if (empty($this->constraintsByModule[$moduleName])) {
            return $affectedTestCases;
        }
        foreach ($this->constraintsByModule[$moduleName] as $constraintName) {
            if (empty($this->constraintToTestCasesMap[$constraintName])) {
                continue;
            }
            foreach ($this->constraintToTestCasesMap[$constraintName] as $testCaseClassName) {
                $testClassModuleName = $this->mapClassNameToModule($testCaseClassName);
                if ($testClassModuleName != $moduleName) {
                    $affectedTestCases[$testCaseClassName] = $testCaseClassName;
                }
            }
        }

        return $affectedTestCases;
    }

    /**
     * Return an array of test cases that uses the specified constraint
     *
     * @param string $constraint
     * @return array
     */
    public function getTestCasesByConstraintReference($constraint)
    {
        if (!isset($this->constraintConfig)) {
            $this->initConstraintConfig();
        }

        if (empty($this->constraintToTestCasesMap[$constraint])) {
            return [];
        } else {
            return $this->constraintToTestCasesMap[$constraint];
        }
    }

    /**
     * Initialize constraint configuration
     *
     * @return void
     */
    protected function initConstraintConfig()
    {
        $this->constraintConfig = $this->constraintFactory->getConfiguration();
        foreach ($this->constraintConfig as $constraintName => $constraintConfig) {
            $module = $constraintConfig['module'];
            $this->constraintsByModule[$module][] = $constraintName;
        }

        $this->constraintToTestCasesMap = $this->mapConstraintToTestCase();
    }

    /**
     * Create a mapping between constraint to testcases
     *
     * @return array
     */
    protected function mapConstraintToTestCase()
    {
        $constraintToTestClassMap = [];

        $testDataSets = $this->getTestDataSets();

        foreach ($testDataSets as $testClassName => $dataSets) {
            foreach ($dataSets as $rows) {
                foreach ($rows as $row) {
                    if (isset($row['constraint'])) {
                        $constraints = explode(',', $row['constraint']);
                        foreach ($constraints as $constraint) {
                            $constraint = trim($constraint);
                            $constraintToTestClassMap[$constraint][$testClassName] = $testClassName;
                        }
                    }
                }
            }
        }
        return $constraintToTestClassMap;
    }

    /**
     * Read all the test data sets in the csv files under test directory
     *
     * @return array
     */
    protected function getTestDataSets()
    {
        $dataSets = [];

        $directories = glob(MTF_TESTS_PATH . '/*/*/Test/TestCase');
        foreach ($directories as $directory) {
            $dirIterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
                ),
                '/.csv/i'
            );
            /** @var $fileInfo \SPLFileInfo */
            foreach ($dirIterator as $fileInfo) {
                $testMethodName = $fileInfo->getBasename('.csv');
                $path = $fileInfo->getPath();
                $testClassName = str_replace('/', '\\', str_replace(MTF_TESTS_PATH, '', $path));

                $testClassName = trim($testClassName, '\\');
                $dataSets[$testClassName][$testMethodName] = $this->readCsv($fileInfo->getRealPath());
            }
        }
        return $dataSets;
    }

    /**
     * Read the test data sets into array
     *
     * @param string $testDataSetFilePath
     * @return array
     */
    protected function readCsv($testDataSetFilePath)
    {
        $handle = fopen($testDataSetFilePath, 'r');

        $data = $header = [];

        while (($line = fgetcsv($handle, 10000, ';', '"', '\\')) !== false) {
            if ($header) {
                $data[] = array_combine($header, $line);
            } else {
                $header = $line;
            }
        }

        return $data;
    }
}

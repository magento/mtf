* Fixed getting configuration path from environment variable
* Fixed an issue with default timeout
* Sync factory class from Magento/Framework/ObjectManager/Factory
* Fixed issue with applying placeholders to selectors in fixture data mapping
* Fixed an issue with method keys() in \Mtf\Client\Driver\Selenium\Element
* Added 'Interface' suffix to all interface names within Magento/Framework directory

1.0.0-dev.8
=============
* Replaced license and copyright placeholders with appropriate license information
* Fixed an issue where the test will crash when user name and password is incorrect
* Added environment variable module_filter_strict to limit test cases to specified modules

1.0.0-dev.7
=============
* Fixed issues with waiting for element in parallel mode
* Added logic to run a subset of tests based on affected modules
* Added logic to get a list of enabled modules in target Magento instance
* Changed module file resolver to only search files in modules that are enabled in target Magento instance
* Added dynamic generation of page classes based on enabled modules in target Magento instance
* Changed path to generated page classes
* Implemented recursive re-initialization for Block
* Implemented scenario configuration reader
* Implemented recursive merge for injectable fixture data from repository and variations(*.csv file)
* Updated scenario configuration reader and fixed issues with preparing step sequence

1.0.0-dev.6
=============
* Added support for MTF Reporting for the parallel running mode
* Fixed issues with running injectable tests in the parallel mode
* Added exception handling to prevent build interruption
* Rewrote the page generator to collect related blocks from all modules
* Created mechanism for running test cases using the scenario approach

1.0.0-dev.5
=============
* Added an ability to skip variation execution by specifying a value in 'issue' column in data set file
* Updated block/fixture/page/handler generators to created file without @package annotation
* Updated 'base' event preset configuration to take screenshots on failures
* Fixed bugs:
  * Fixed an issue with second test case failure if for a fixture persist the same UI handler is used

1.0.0-dev.4
=============
* Fixed bugs:
  * Fixed an issue where an exception was thrown while running functional tests in the parallel mode
  * Fixed an issue where the getElements() method performed the search of elements in a specified context incorrectly
  * For a typified <select> element added the ability to select strict and non-strict values
  * Fixed an issue where an exception was thrown when running injectable tests in the parallel mode

1.0.0-dev.3
=============
* Created Event Management and observers structure
  * Added logger to store errors and events
  * Added logger for html source code
  * Added screenshots capturing on events
  * Added logger for client js errors
  * Added exception handler logger
* Move to phpunit 4.x
  * Set the version requirement to be >= 3.7, both 3.7.x and 4.x phpunit should be acceptable
  * Add addRiskyTest function to StateListener, the phpunit 4.x interface PHPUnit_Framework_TestListener requires it
* Fixed bugs:
  * Fixed an issue with using specified class as typified element
  * Fixed an issue with running Injectable tests on PHPUnit 4.1.0
* Added getElements() method which returns an array of wrappedElements by specified locator
* Added an ability to return objects from test case method
* Added an ability to skip field in *.csv data set file for any array depth

1.0.0-dev.2
=============
* Added requirements section into a composer.json file
* Updated README.md file with User Documentation
  * Running the Magento Test Framework (MTF)
  * Installing and Configuring the Magento Test Framework (MTF)
* MTF Improvements
  * Fixed Mtf\Fixture\InjectableFixture::__construct() method
  * Moved __prepare() method call from __construct() to run() method in Mtf\Constraint\AbstractConstraint\Injectable class
  * Added validations for Mtf\Block\Form class methods
  * Updated return value for Mtf\Client\Driver\Selenium\Element\CheckboxElement::getValue() method
* Fixed bugs:
  * Fixed an issue in Mtf\ObjectManager\Factory with resolving arguments in __construct() during new instance creation

1.0.0-dev.1
=============
* Added initial version of MTF to public repository

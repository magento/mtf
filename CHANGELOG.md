1.0.0-rc.39
===========
* Throw an exception with text error message for the cURL operation
* Added an ability to replace variations and fixtures using 'replace' attribute
* Added fallback mechanism to generators
* Added an ability to wait the element before finding it

1.0.0-rc.38
===========
* Added an ability to use the same steps in scenarios several times using 'alias' attribute

1.0.0-rc.37
===========
* Updated radio button element to get and set value based on label

1.0.0-rc.36
===========
* Added constraint execution logging to system events observer

1.0.0-rc.35
===========
* Updated README.md
* Added radio button typified element  
* Fixed sending parameters using PUT method

1.0.0-rc.34
===========
* Set ticketId attribute for testCase as optional

1.0.0-rc.33
=============
* Added mage_mode environment variable support in functional test

1.0.0-rc.32
=============
* Wait for an element while getting its value on the form
* Added *.dist file for global configuration
* Set CurlInterface as non shared object
* Provided framework with CurlTransport class
* Created handler fallback in config.xml. Moved constants defining from ObjectManagerFactory to bootstrap.php
* Moved selenium server dependency from require section to suggest section.
* Added an ability to hover over element

1.0.0-rc.31
=============
* Updated variation.xsd for new attributes in test case xml files

1.0.0-rc.30
=============
* Code adaptation to PHP7

1.0.0-rc.29
=============
* Trigger change event after setValue()

1.0.0-rc.28
=============
* Moved all test data to repositories

1.0.0-rc.27
=============
* Added wait to form element before filling a value
* Display variation name in PHPUnit test result log file

1.0.0-rc.26
=============
* Add an ability to specify several values for one tag type
* Removed code duplication in generators
* Stability improvement for dynamic JavaScript forms interaction

1.0.0-rc.25
=============
* Fixed that incomplete tests are displayed as passed in test results list
* Added mechanism of cleaning up data in steps after scenario execution
* Optimized getValue method for select element

1.0.0-rc.24
=============
* Added an ability to generate pages for external solutions

1.0.0-rc.23
=============
* Added an ability to merge form mapping from different modules

1.0.0-rc.22
=============
* Fixed config data structure
* Updated credential template file
* Fixed module filter for test suite rules

1.0.0-rc.21
=============
* Unified configuration usage
* Performed general code clean up
* Updated license information

1.0.0-rc.20
=============
* Fixed losing focus from the iframe during setting value to element

1.0.0-rc.19
=============
* Introduced unified format for Fixtures configuration
* Introduced unified format for Repositories configuration
* Implemented dynamic entities generation

1.0.0-rc.18
=============
* Fixed wrong tests number is displayed for functional tests on Bamboo
* Fixed fatal error after create screenshot or report on Windows
* Implemented mechanism of applying 3rd party credentials
* Fixed setValue() method for StrictselectElement class
* Fixed an issue with applying variations rules in parallel run after configuration unification

1.0.0-rc.17
=============
* Unified Format for pages configuration
* Unified Format for TestCase Variations, TestCase Scenario declarations

1.0.0-rc.16
=============
* Unified Format for framework configuration
* Unified Format for TestCase Variations, TestCase Scenario declarations

1.0.0-rc.15
=============
* Fixed wrong screenshot is taken for test failure if tearDown() method is present
* Implemented fixture and repository merger
* Changed generators of fixtures and repositories
* Updated setValue() method to trigger JS events

1.0.0-rc.14
=============
* Magento vendor name is used as root MTF namespace

1.0.0-rc.13
=============
* Added ability to skip injectable test including prepare and inject methods
* Removed dependency on Magento Framework
* Added alternative web driver support and adapter for Facebook web driver as an example

1.0.0-rc.12
=============
* Fixed issue with applying tagging mechanism on variation scope in parallel mode
* Code marked with @SuppressWarnings annotations

1.0.0-rc.11
=============
* Fixed tagging mechanism for filter variations by tag
* Changed file iterators to work with symlinks
* Fixed wrong test names of MTF Corpuscular tests in parallel mode on bamboo
* Added workaround for selenium issue https://code.google.com/p/selenium/issues/detail?id=3544
* Added workaround for selenium issue https://code.google.com/p/selenium/issues/detail?id=5165

1.0.0-rc.10
=============
* Implemented tagging functionality to run custom test scope
* Fixed getting configuration path from environment variable
* Fixed an issue with default timeout
* Fixed handling for Incomplete and Skipped tests in parallel mode
* Fixed issues when final tests were missed in report in parallel mode

1.0.0-rc.9
=============
* Sync factory class from Magento/Framework/ObjectManager/Factory
* Fixed issue with applying placeholders to selectors in fixture data mapping
* Fixed an issue with method keys() in \Magento\Mtf\Client\Driver\Selenium\Element
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

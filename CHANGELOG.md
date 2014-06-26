* Created Event Management and observers structure
  * Added logger to store errors and events
  * Added logger for html source code
  * Added screenshots capturing on events
  * Added logger for client js errors
  * Added exception handler logger
* Added getElements() method which returns an array of wrappedElements by specified locator
* Added an ability to return objects from test case method
* Fixed bugs:
  * Fixed an issue with using specified class as typified element

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

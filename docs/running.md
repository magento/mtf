# Running the Magento Test Framework (MTF)

This page discusses how to run tests using the MTF.

## Prerequisite

Install and configure the MTF as discussed in [Installing and Configuring the Magento Test Framework (MTF)](install-config.md).

## Running MTF

1.	Make sure your <a href="https://github.com/magento/magento2" target="_blank">Magento 2 code</a> is up-to-date.
	
2.	Change to the `dev/tests/functional` directory and run `composer update` 

	`composer update` updates any dependencies that otherwise prevent tests from running successfully. 

3.	Run the Selenium Server. The Selenium Server will drive a browser to execute your tests. You can download the latest Selenium Server from [Selenium project website](http://www.seleniumhq.org/download/).

    Specific versions of the Selenium Server are compatible with specific versions of browsers. [Read more about compatibility of browser version and Selenium server version.](http://docs.seleniumhq.org/about/platforms.jsp)

    Enter in terminal:

	```
	java -jar <path_to_selenium_directory>/selenium-server.jar
	```

3.	Start your tests using PHPUnit. This can be done using your IDE or the command line.

	Example using the command line:

	```
	cd dev/tests/functional
	phpunit
	```

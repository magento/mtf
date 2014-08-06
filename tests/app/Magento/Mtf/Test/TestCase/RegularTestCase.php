<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mtf\Test\TestCase;

use Mtf\TestCase\Functional;

/**
 * Class RegularTestCase
 */
class RegularTestCase extends Functional
{
    /**
     * @depends Magento\Mtf\Test\TestCase\InjectableTestCase::test1
     * @return void
     */
    public function test1()
    {
        /** @var $fixture \Magento\Mtf\Test\Fixture\Test */
        $fixture = $this->objectManager->create('Magento\Mtf\Test\Fixture\Test');

        /** @var $page \Magento\Mtf\Test\Page\Area\TestPage */
        $page = $this->objectManager->create('Magento\Mtf\Test\Page\Area\TestPage');

        $page->open();
        $page->getTestBlock()->click($fixture);
        sleep(1);
    }

    /**
     * @dataProvider dataProvider
     * @param string $fromDataProvider
     * @return void
     */
    public function test2($fromDataProvider)
    {
        var_dump($fromDataProvider . " works well!");
        /** @var $fixture \Magento\Mtf\Test\Fixture\Test */
        $fixture = $this->objectManager->create('Magento\Mtf\Test\Fixture\Test');

        /** @var $page \Magento\Mtf\Test\Page\Area\TestPage */
        $page = $this->objectManager->create('Magento\Mtf\Test\Page\Area\TestPage');

        $page->open();
        $page->getTestBlock()->click($fixture);
        sleep(1);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            'Variation #1' => array('Data Provider (1) for Regular Test Case'),
            'Variation #2' => array('Data Provider (2) for Regular Test Case')
        );
    }
}

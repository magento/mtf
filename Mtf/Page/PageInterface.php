<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Page;

use Mtf\Fixture\FixtureInterface;
use Mtf\Block\BlockInterface;

/**
 * Interface for Pages
 *
 * @api
 */
interface PageInterface
{
    /**
     * Prepare page according to fixture data
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function init(FixtureInterface $fixture);

    /**
     * Open the page URL in browser
     *
     * @param array $params [optional]
     * @return $this
     */
    public function open(array $params = []);

    /**
     * Retrieve an instance of block
     *
     * @param string $blockName
     * @return BlockInterface
     */
    public function getBlockInstance($blockName);
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate\Repository;

/**
 * Class DummyCollectionProvider
 *
 * @package Mtf\Util\Generate\Repository
 * @internal
 */
class DummyCollectionProvider implements CollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCollection(array $fixture)
    {
        return [];
    }
}

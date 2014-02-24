<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate\Fixture;

/**
 * Class DummyFieldsProvider
 *
 * @package Mtf\Util\Generate\Fixture
 * @internal
 */
class DummyFieldsProvider implements FieldsProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFields(array $fixture)
    {
        return [];
    }
}
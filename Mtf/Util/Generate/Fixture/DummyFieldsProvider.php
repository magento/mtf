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
 * @internal
 */
class DummyFieldsProvider implements FieldsProviderInterface
{
    /**
     * Collect fields for given fixture
     *
     * @param array $fixture
     * @return array
     */
    public function getFields(array $fixture)
    {
        return [];
    }
}

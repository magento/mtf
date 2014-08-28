<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate\Fixture;

/**
 * Class FieldsProviderInterface
 *
 * @api
 */
interface FieldsProviderInterface
{
    /**
     * Collect fields for given fixture
     *
     * @param array $fixture
     * @return array
     */
    public function getFields(array $fixture);
}

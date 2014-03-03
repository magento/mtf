<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument;

/**
 * Recoverable situation of a missing argument value, presence of which is optional according to the business logic.
 * Possible resolution is to use a default argument value, if there is one.
 */
class MissingOptionalValueException extends \RuntimeException
{
}

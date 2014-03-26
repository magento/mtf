<?php
/**
 * Date filter. Converts date from localized to internal format.
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Stdlib\DateTime\Filter;

use Magento\Stdlib\DateTime\TimezoneInterface;

class Date implements \Zend_Filter_Interface
{
    /**
     * Filter that converts localized input into normalized format
     *
     * @var \Zend_Filter_LocalizedToNormalized
     */
    protected $_localToNormalFilter;

    /**
     * Filter that converts normalized input into internal format
     *
     * @var \Zend_Filter_NormalizedToLocalized
     */
    protected $_normalToLocalFilter;

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param TimezoneInterface $localeDate
     */
    public function __construct(TimezoneInterface $localeDate)
    {
        $this->_localeDate = $localeDate;
        $this->_localToNormalFilter = new \Zend_Filter_LocalizedToNormalized(
            array('date_format' => $this->_localeDate->getDateFormat(TimezoneInterface::FORMAT_TYPE_SHORT))
        );
        $this->_normalToLocalFilter = new \Zend_Filter_NormalizedToLocalized(
            array('date_format' => \Magento\Stdlib\DateTime::DATE_INTERNAL_FORMAT)
        );
    }

    /**
     * Convert date from localized to internal format
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return $this->_normalToLocalFilter->filter($this->_localToNormalFilter->filter($value));
    }
}

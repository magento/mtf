<?php
/**
 * Date/Time filter. Converts datetime from localized to internal format.
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Stdlib\DateTime\Filter;

class DateTime extends Date
{
    /**
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(\Magento\Stdlib\DateTime\TimezoneInterface $localeDate)
    {
        parent::__construct($localeDate);
        $this->_localToNormalFilter = new \Zend_Filter_LocalizedToNormalized(
            array(
                'date_format' => $this->_localeDate->getDateTimeFormat(
                    \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT
                )
            )
        );
        $this->_normalToLocalFilter = new \Zend_Filter_NormalizedToLocalized(
            array('date_format' => \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT)
        );
    }
}

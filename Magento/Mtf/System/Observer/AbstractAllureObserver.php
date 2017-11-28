<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Mtf\System\Observer;

use Yandex\Allure\Adapter\Support\AttachmentSupport;

/**
 * Abstract allure observer.
 */
abstract class AbstractAllureObserver extends AbstractObserver
{
    use AttachmentSupport;
}

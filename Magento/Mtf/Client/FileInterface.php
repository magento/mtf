<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client;

use Magento\Mtf\Client\Element\UploadElement;

/**
 * File interface for upload/download actions.
 */
interface FileInterface
{
    /**
     * Upload file.
     *
     * @param UploadElement $element
     * @param string $filePath
     * @return void
     */
    public function upload(UploadElement $element, $filePath);

    /**
     * Download file using attribute value in the specified element.
     *
     * @param ElementInterface $element
     * @param string $attribute src|url|targetUrl
     * @return mixed
     */
    public function download(ElementInterface $element, $attribute);
}

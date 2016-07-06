<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Client;

use Magento\Mtf\Client\Element\UploadElement;

/**
 * Class for upload/download actions.
 */
class File implements FileInterface
{
    /**
     * Upload file.
     *
     * @param UploadElement $element
     * @param string $filePath
     * @return void
     * @throws \Exception
     */
    public function upload(UploadElement $element, $filePath)
    {
        $element->setValue($filePath);
    }

    /**
     * Download file using attribute value in the specified element.
     *
     * @param ElementInterface $element
     * @param string $attribute src|url|targetUrl
     * @return mixed
     * @throws \Exception
     */
    public function download(ElementInterface $element, $attribute)
    {
        $url = $element->getAttribute($attribute);
        $fileData = file_get_contents($url);
        if ($fileData === false) {
            throw new \Exception("File by specified path '$url' isn't exist.");
        }

        return $fileData;
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Mtf\Util\Protocol;

/**
 * Curl protocol interface.
 */
interface CurlInterface
{
    /**
     * HTTP request methods.
     */
    const GET = 'GET';
    const PUT = 'PUT';
    const POST = 'POST';

    /**
     * Add additional option to cURL.
     *
     * @param int $option
     * @param mixed $value
     * @return $this
     */
    public function addOption($option, $value);

    /**
     * Send request to the remote server.
     *
     * @param string $url
     * @param mixed $params
     * @param string $method
     * @param mixed $headers
     * @return void
     */
    public function write($url, $params = [], $method = CurlInterface::POST, $headers = []);

    /**
     * Read response from server.
     *
     * @return string
     */
    public function read();

    /**
     * Close the connection to the server.
     *
     * @return void
     */
    public function close();
}

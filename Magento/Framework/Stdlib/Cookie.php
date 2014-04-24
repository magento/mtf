<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Stdlib;

/**
 * Core cookie model
 */
class Cookie
{
    /**
     * Set cookie
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param bool|int|string $secure
     * @param bool|string $httponly
     * @return $this
     */
    public function set($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        /**
         * Check headers sent
         */
        if (headers_sent()) {
            return $this;
        }

        if ($period === true) {
            $period = 3600 * 24 * 365;
        }

        if ($period == 0) {
            $expire = 0;
        } else {
            $expire = time() + $period;
        }

        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

        return $this;
    }

    /**
     * Postpone cookie expiration time if cookie value defined
     *
     * @param string $name The cookie name
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param bool|int|string $secure
     * @param string|bool $httponly
     * @return $this
     */
    public function renew($name, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if ($period === null) {
            return $this;
        }
        $value = $this->get($name, false);
        if ($value !== false) {
            $this->set($name, $value, $period, $path, $domain, $secure, $httponly);
        }
        return $this;
    }

    /**
     * Retrieve cookie or false if not exists
     *
     * @param string|null $name
     * @param mixed|null $default
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        if (null === $name) {
            return $_COOKIE;
        }
        return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $default;
    }
}

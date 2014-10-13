<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util;

use Mtf\System\Config;

/**
 * Class ModulePathResolver, resolve module path based on enabled modules of target Magento instance
 *
 * @api
 */
class ModuleResolver
{
    /**
     * @var array
     */
    protected $enabledModules;

    /**
     * @var \Mtf\System\Config
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $adminTokenUrl = "rest/V1/integration/admin/token";

    /**
     * @var string
     */
    protected $moduleUrl = "rest/V1/modules";

    /**
     * Constructor
     *
     * @param Config $configuration
     */
    public function __construct(Config $configuration = null)
    {
        if ($configuration) {
            $this->configuration = $configuration;
        } else {
            $this->configuration = new Config();
        }
    }

    /**
     * Return an array of enabled modules of target Magento instance
     *
     * @return array
     */
    public function getEnabledModules()
    {
        if (!empty($this->enabledModules)) {
            return $this->enabledModules;
        }

        $token = $this->getAdminToken();
        $url = $_ENV['app_frontend_url'] . $this->moduleUrl;

        $headers = [
            'Authorization: Bearer ' . $token,
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        $this->enabledModules = json_decode($response);
        return $this->enabledModules;
    }

    /**
     * Return the modules path based on which modules are enabled in the target Magento instance
     *
     * @return array
     */
    public function getModulesPath()
    {
        $enabledModules = $this->getEnabledModules();
        $directories = '{';
        foreach ($enabledModules as $module) {
            $directoryName = explode('_', $module)[1];
            $directories = $directories . $directoryName;
            $directories = $directories . ',';
        }
        $directories = rtrim($directories, ',');
        $directories = $directories . '}';
        $pattern = MTF_TESTS_PATH . '*/' . $directories ;
        return glob($pattern, GLOB_BRACE);
    }

    /**
     * Get the API token for admin
     *
     * @return string
     */
    protected function getAdminToken()
    {
        $credentials = $this->configuration->getConfigParam('application/backend_user_credentials');
        $url = $_ENV['app_frontend_url'] . $this->adminTokenUrl;
        $data = [
            'username' => $credentials['login'],
            'password' => $credentials['password']
        ];
        $headers = [
            'Content-Type: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        return json_decode($response);
    }
}

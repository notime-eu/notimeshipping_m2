<?php

namespace Notime\Shipping\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**#@+
     * Constants for config path.
     */
    const XML_PATH_CARRIERS_NOTIME_OCP_APIM_SUBSCRIPTION_KEY = 'carriers/notime/ocp_apim_subscription_key';
    const XML_PATH_CARRIERS_NOTIME_WIDGET_EDITMODE_CODE = 'carriers/notime/widget_editmode_code';
    const XML_PATH_CARRIERS_NOTIME_WIDGET_EDITMODE_BUTTON = 'carriers/notime/widget_editmode_button';
    const XML_PATH_CARRIERS_NOTIME_ACTIVE = 'carriers/notime/active';
    /**#@-*/

    /**
     * Scope Config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get config settings by path
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Key
     * @param int $storeId
     * @return string
     */
    public function getKey($storeId = null)
    {
        return $this->getConfig(self::XML_PATH_CARRIERS_NOTIME_OCP_APIM_SUBSCRIPTION_KEY, $storeId);
    }

    /**
     * Get Edit Mode Code
     *
     * @param int $storeId
     * @return string
     */
    public function getEditModeCode($storeId = null)
    {
        return $this->getConfig(self::XML_PATH_CARRIERS_NOTIME_WIDGET_EDITMODE_CODE, $storeId);
    }

    /**
     * Get Edit Mode Button
     *
     * @param int $storeId
     * @return string
     */
    public function getEditModeButton($storeId = null)
    {
        return $this->getConfig(self::XML_PATH_CARRIERS_NOTIME_WIDGET_EDITMODE_BUTTON, $storeId);
    }

    /**
     * Get Active Flag
     *
     * @param int $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return $this->getConfig(self::XML_PATH_CARRIERS_NOTIME_ACTIVE, $storeId);
    }
}
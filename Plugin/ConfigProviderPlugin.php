<?php

namespace Notime\Shipping\Plugin;

use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\Model\AbstractModel;
use Notime\Shipping\Model\Config;

class ConfigProviderPlugin extends AbstractModel
{
    /**
     * Checkout session
     *
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Data Config
     *
     * @var Config
     */
    protected $config;

    /**
     * ConfigProviderPlugin constructor.
     *
     * @param Session $checkoutSession
     * @param Config $config
     */
    public function __construct(
        Session $checkoutSession,
        Config $config
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
    }

    /**
     * After get Config Plugin
     *
     * @param DefaultConfigProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetConfig(DefaultConfigProvider $subject, array $result)
    {
        $widgetEditModeCode = $this->config->getEditModeCode();
        $widgetEditModeButton = $this->config->getEditModeButton();

        if ($widgetEditModeCode && $widgetEditModeButton) {
            preg_match('/.*groupId=(.*?)&.*/im', $widgetEditModeCode, $matches);
            $result['quoteData']['notimeJSgroupId'] = $matches[1];
            $result['quoteData']['notimeWidgetButton'] = $widgetEditModeButton;
        }

        return $result;
    }

}
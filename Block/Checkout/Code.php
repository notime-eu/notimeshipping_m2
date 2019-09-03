<?php

namespace Notime\Shipping\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use Notime\Shipping\Model\Config;

class Code extends Template
{
    /**
     * Checkout Session
     *
     * @var Session
     */
    private $checkoutSession;

    /**
     * Cart Repository
     *
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Locale
     *
     * @var Magento\Framework\Locale\Resolver
     */
    private $locale;

    /**
     * Config
     *
     * @var Config
     */
    private $config;

    public function __construct(
        Template\Context $context,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Locale\Resolver $locale,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->locale = $locale;
        $this->config = $config;
    }

    /**
     * Init Shipment ID
     *
     * @return void
     */
    public function init()
    {
        $quote = $this->checkoutSession->getQuote();
        $notimeShipmentId = $quote->getNotimeShipmentId();

        if (!$notimeShipmentId) {
            $this->checkoutSession->setData('notime_fee', 0);
        }
    }

    /**
     * Get Language Code
     *
     * @return string
     */
    public function getLanguage()
    {
        $locale = $this->locale->getLocale();

        switch ($locale) {
            case 'de_DE':
                $code = 'de';
                break;
            case 'fr_FR':
                $code = 'fr';
                break;
            default:
                $code = 'en';
        }
        return $code;
    }

    /**
     * Get Active flag
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->config->isActive();
    }
}
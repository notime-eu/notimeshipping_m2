<?php

namespace Notime\Shipping\Model\Carrier;
 
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Notime extends AbstractCarrier implements CarrierInterface
{
    /**
     * Method code
     *
     * @var string
     */
    protected $_code = 'notime';

    /**
     * Rate Result Factory
     *
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * Rate Method Factory
     *
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Checkout Session
     *
     * @var Session
     */
    private $checkoutSession;

    /**
     * Notime constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }
 
    /**
     * Get Allowed methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['notime' => $this->getConfigData('name')];
    }
 
    /**
     * Collect rates
     *
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
 
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
 
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();
 
        $method->setCarrier('notime');
        $method->setCarrierTitle($this->getConfigData('title'));
 
        $method->setMethod('notime');
        $method->setMethodTitle($this->getConfigData('name'));
        $fee = $this->getConfigData('price');

        if ($this->checkoutSession->getData('notime_fee')) {
            $fee = $this->checkoutSession->getData('notime_fee');
        }

        $amount = $fee;

        $method->setPrice($amount);
        $method->setCost($amount);
 
        $result->append($method);
 
        return $result;
    }
}
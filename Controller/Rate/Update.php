<?php

namespace Notime\Shipping\Controller\Rate;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Api\CartRepositoryInterface;

class Update extends Action
{
    /**
     * Checkout Session
     *
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Result Json
     *
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Cart Repository
     *
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * Update constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        JsonFactory $resultJsonFactory,
        CartRepositoryInterface $cartRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Set shipping price for notime shipping
     *
     * @throws \Exception
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!isset($data['fee'])) {
            return $result;
        }

        $this->checkoutSession->setData('notime_fee', $data['fee']);
        $quote = $this->checkoutSession->getQuote();
        $quote->setNotimeShipmentId($data['shipmentId']);
        $quote->setNotimeTimewindowDate($data['timeWindowDate']);
        $quote->setNotimeServiceId($data['serviceId']);
        $quote->setNotimeShipmentTime($data['shipmentTime']);
        $this->cartRepository->save($quote);

        $address = $quote->getShippingAddress();
        $address->setCollectShippingRates(true)->save();

        return $result;
    }
}

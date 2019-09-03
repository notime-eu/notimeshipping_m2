<?php

namespace Notime\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{

    /**
     * Quote observer
     *
     * @param Observer $observer
     * @throws \Exception
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        /** @var \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order*/
        $order = $observer->getOrder();

        if ($quote->getNotimeShipmentId()) {
            $order->setNotimeShipmentId($quote->getNotimeShipmentId());
            $order->addStatusHistoryComment(__('Notime->ShipmentId: %1', $quote->getNotimeShipmentId()));
            $order->save();
        }
    }
}
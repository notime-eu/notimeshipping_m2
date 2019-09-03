<?php

namespace Notime\Shipping\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Notime\Shipping\Logger\Logger;
use Notime\Shipping\Model\Config;
use Notime\Shipping\Service\Connector\ApproveConnector;
use Notime\Shipping\Service\Connector\StatusConnector;

class OrderPlaceAfter implements ObserverInterface
{


    protected $config;

    /**
     * ApproveConnector
     *
     * @var ApproveConnector
     */
    private $approveConnector;

    /**
     * StatusConnector
     *
     * @var StatusConnector
     */
    private $statusConnector;

    /**
     * Logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * Checkout session
     *
     * @var Session
     */
    private $checkoutSession;

    /**
     * Timezone
     *
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * OrderPlaceAfter constructor.
     * @param ApproveConnector $approveConnector
     * @param StatusConnector $statusConnector
     * @param Session $checkoutSession
     * @param TimezoneInterface $timezone
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        ApproveConnector $approveConnector,
        StatusConnector $statusConnector,
        Session $checkoutSession,
        TimezoneInterface $timezone,
        Config $config,
        Logger $logger
    ) {

        $this->config = $config;
        $this->approveConnector = $approveConnector;
        $this->statusConnector = $statusConnector;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->timezone = $timezone;
    }

    /**
     * Save Notime Info
     *
     * @param Observer $observer
     * @throws \Exception
     * @return void
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order $order */
        $order = $event->getOrder();

        $shippingMethod = $order->getShippingMethod();
        $shipmentId = $order->getNotimeShipmentId();

        if ($shippingMethod == 'notime_notime') {
            if (!empty($shipmentId)) {
                try {
                    $this->approveConnector->init($shipmentId);
                    $this->approveConnector->setData($order);
                    $approveResult = $this->approveConnector->sendRequest();
                    if ($approveResult->ResultCode === 0) {
                        $this->statusConnector->init($shipmentId);
                        $statusResult = $this->statusConnector->sendRequest();
                        $timeFrom = $this->convertTime($statusResult->DeliveryTimeFrom);
                        $timeTo   = $this->convertTime($statusResult->DeliveryTimeTo);
                        $info = $statusResult->DeliveryDate. ' '.$timeFrom. ' - '.$timeTo;

                        $order->setShippingDescription(
                            $order->getShippingDescription()
                            . __('| Info: %1', $info)
                        );

                        $order->addStatusHistoryComment(
                            __('Notime->Success: Shipment was approved successfully! Delivery: %1', $info)
                        )
                            ->setIsCustomerNotified(false)
                            ->save();
                    } else {
                        $order->addStatusHistoryComment(
                            __('Notime->Success: Shipment was not approved! %1',
                                $approveResult->ErrorString)
                        )
                            ->setIsCustomerNotified(false)
                            ->save();
                        $this->logger->error(sprintf(__("Notime_Shipping: Approve error: %s"), $approveResult->ErrorString));
                    }

                    $this->checkoutSession->setData('notime_fee', 0);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }

            } else {
                $this->logger->error(__('Notime_Shipping: Notime Shipment ID Doesn\'t exist'));
            }
        }

    }

    /**
     * Convert utc to timezone
     *
     * @param string $time
     * @return string
     */
    protected function convertTime($time)
    {
        $time = $this->timezone->date(new \DateTime($time));
        return $time->format('H:i:s');
    }
}
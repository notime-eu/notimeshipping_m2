<?php

namespace Notime\Shipping\Service\Connector;

use Magento\Framework\HTTP\ZendClient;

class ApproveConnector extends GenericConnector
{
    /**
     * Connection url
     *
     * @var string
     */
    protected $url = 'https://v1.notimeapi.com/api/shipment/approve';

    /**
     * Connection method
     *
     * @var string
     */
    protected $method = ZendClient::POST;

    /**
     * Set Data for Approve request
     *
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     * @throws \Exception
     * @return void
     */
    public function setData($order)
    {
        $shippingAddress = $order->getShippingAddress();
        $data = [
            'ShipmentId' => $this->shipmentId,
            'Reference' => $order->getIncrementid().'('.$order->getId().')',
            'Dropoff' => [
                'Name' => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                'Phone' => $shippingAddress->getTelephone(),
                'ContactEmailAddress' => $shippingAddress->getEmail(),
                'City' => $shippingAddress->getCity(),
                'CountryCode' => $shippingAddress->getCountryId(),
                'Postcode' => $shippingAddress->getPostcode(),
                'Streetaddress' => implode(' ', $shippingAddress->getStreet()),
            ],
            'EndUser' => [
                'FullName' => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname(),
                'Phone' => $shippingAddress->getTelephone(),
                'Email' => $shippingAddress->getEmail(),
            ]
        ];

        $this->addBody(json_encode($data));
    }

}
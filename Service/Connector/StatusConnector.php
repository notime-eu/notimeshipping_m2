<?php

namespace Notime\Shipping\Service\Connector;

class StatusConnector extends GenericConnector
{
    /**
     * Connection url
     *
     * @var string
     */
    protected $url = 'https://v1.notimeapi.com/api/shipment/{shipmentId}/status';

    /**
     * {@inheritdoc}
     */
    protected function getUrl()
    {
        return str_replace('{shipmentId}', $this->shipmentId, $this->url);
    }

}
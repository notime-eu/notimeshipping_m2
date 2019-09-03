<?php

namespace Notime\Shipping\Service\Connector;

use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Notime\Shipping\Logger\Logger;
use Notime\Shipping\Model\Config;

class GenericConnector
{
    const CONNECTION_TIMEOUT = 45;
    const MAX_REDIRECTS = 0;
    const KEY = 'Ocp-Apim-Subscription-Key';
    const CONTENT_TYPE = 'Content-Type';

    /**
     * Connection URL
     *
     * @var string
     */
    protected $url = '';

    /**
     * Connection Method
     *
     * @var string
     */
    protected $method = ZendClient::GET;

    /**
     * Shipment ID
     *
     * @var string
     */
    protected $shipmentId;

    /**
     * Http Zend Client Factory
     *
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * Http Zend Client
     *
     * @var ZendClient
     */
    protected $client;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Connector constructor.
     *
     * @param ZendClientFactory $httpClientFactory
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        ZendClientFactory $httpClientFactory,
        Config $config,
        Logger $logger
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Init Client
     *
     * @param string $shipmentId
     * @throws \Exception
     * @return ZendClient
     */
    public function init($shipmentId = '')
    {
        if (!$this->client) {
            try {
                $this->shipmentId = $shipmentId;
                /* @var ZendClient $client */
                $client = $this->httpClientFactory->create();
                $client->setUri($this->getUrl());
                $client->setMethod($this->method);
                $client->setConfig([
                    'maxredirects' => self::MAX_REDIRECTS,
                    'timeout' => self::CONNECTION_TIMEOUT
                ]);

                $this->client = $client;
                $this->setHeaders();

            } catch (\Zend_Http_Client_Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $this->client;
    }

    /**
     * Get Client
     *
     * @return ZendClient
     * @throws \Exception
     */
    protected function getClient()
    {
        if (!$this->client) {
            throw new \Exception('Http client is not initiated.');
        } else {
            return $this->client;
        }
    }

    /**
     * Adds key to headers of request
     *
     * @throws \Exception
     * @return void
     */
    protected function setAccessKey()
    {
        $key = $this->config->getKey();

        try {
            $this->getClient()->setHeaders(self::KEY, $key);
        } catch (\Zend_Http_Client_Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Adds Content-Type to headers of request
     *
     * @param string $type
     * @throws \Exception
     * @return void
     */
    protected function setContentType($type)
    {
        try {
            $this->getClient()->setHeaders(self::CONTENT_TYPE, $type);
        } catch (\Zend_Http_Client_Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Set Get Params
     *
     * @param string|array $name
     * @param string $value
     * @throws \Exception
     * @return \Zend_Http_Client
     */
    public function setGetParams($name, $value = null)
    {
        return $this->getClient()->setParameterGet($name, $value);
    }

    /**
     * Set Post Params
     *
     * @param string|array $name
     * @param string $value
     * @throws \Exception
     * @return \Zend_Http_Client
     */
    public function setPostParams($name, $value = null)
    {
        return $this->getClient()->setParameterPost($name, $value);
    }

    /**
     * Set body for request
     *
     * @param string $json
     * @return \Zend_Http_Client
     * @throws \Exception
     */
    public function addBody($json)
    {
        return $this->getClient()->setRawData($json, 'application/json');
    }

    /**
     * Sends request
     *
     * @return Object
     * @throws \Exception
     */
    public function sendRequest()
    {
        $responce = '';
        try {
            $responce = $this->getClient()->request()->getBody();
        } catch (\Exception $e) {

        }
        return json_decode($responce);
    }

    /**
     * Reset Current Client
     *
     * @return void
     */
    public function resetClient()
    {
        $this->client = null;
    }

    /**
     * Sets Ocp-Apim-Subscription-Key for Client
     *
     * @throws \Exception
     * @return void
     */
    protected function setHeaders()
    {
        $this->setAccessKey();
        $this->setContentType('application/json');
    }

    /**
     * Get Url
     *
     * @return string
     */
    protected function getUrl()
    {
        return $this->url;
    }
}

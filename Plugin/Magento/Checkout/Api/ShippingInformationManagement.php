<?php

namespace Notime\Shipping\Plugin\Magento\Checkout\Api;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface as Subject;
use Magento\Quote\Api\CartRepositoryInterface;


class ShippingInformationManagement
{
    /**
     * Quote Repository
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->quoteRepository = $cartRepository;
    }

    /**
     * Before Save Address Information
     *
     * @param Subject $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        Subject $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        if ($extensionAttributes = $addressInformation->getExtensionAttributes()) {
            if ($notimeShipmentId = $extensionAttributes->getNotimeShipmentId()) {
                /** @var \Magento\Quote\Model\Quote $quote */
                $quote = $this->quoteRepository->getActive($cartId);
                $quote->setData('notime_shipment_id', $notimeShipmentId);
            }

        }
        return [$cartId, $addressInformation];
    }
}
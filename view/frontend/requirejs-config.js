var config = {
    map:{
      '*': {
          'Magento_Checkout/js/model/shipping-service':'Notime_Shipping/js/model/shipping-service'
      }
    },
    config:{
        mixins:{
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'Notime_Shipping/js/view/shipping-address/address-renderer/default': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'Notime_Shipping/js/view/shipping-information/address-renderer/default': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Notime_Shipping/js/view/shipping': true
            }
        }
    }
};

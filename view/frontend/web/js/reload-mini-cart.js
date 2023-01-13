define([
    'Magento_Customer/js/customer-data'
], function (customerData) {
    'use strict';
    customerData.reload(['cart'], true);
})

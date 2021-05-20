<?php

class Ccc_Order_Block_Adminhtml_Create_Data extends Ccc_Order_Block_Adminhtml_Create_Abstract
{

    public function getAvailableCurrencies()
    {
        $dirtyCodes = $this->getStore()->getAvailableCurrencyCodes();
        $codes = array();
        if (is_array($dirtyCodes) && count($dirtyCodes)) {
            $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                Mage::app()->getStore()->getBaseCurrency(),
                $dirtyCodes
            );
            foreach ($dirtyCodes as $code) {
                if (isset($rates[$code]) || $code == Mage::app()->getStore()->getBaseCurrencyCode()) {
                    $codes[] = $code;
                }
            }
        }
        return $codes;
    }

    /**
     * Retrieve curency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencyName($code)
    {
        return Mage::app()->getLocale()->currency($code)->getName();
    }

    /**
     * Retrieve curency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencySymbol($code)
    {
        $currency = Mage::app()->getLocale()->currency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * Retrieve current order currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->getStore()->getCurrentCurrencyCode();
    }

}

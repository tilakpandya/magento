<?php

class Ccc_Vendor_Model_Config_Share extends Mage_Core_Model_Config_Data
{
    /**
     * Xml config path to customers sharing scope value
     *
     */
    const XML_PATH_VENDOR_ACCOUNT_SHARE = 'vendor/account_share/scope';
    
    /**
     * Possible customer sharing scopes
     *
     */
    const SHARE_GLOBAL  = 0;
    const SHARE_WEBSITE = 1;

    /**
     * Check whether current customers sharing scope is global
     *
     * @return bool
     */
    public function isGlobalScope()
    {
        return !$this->isWebsiteScope();
    }

    /**
     * Check whether current customers sharing scope is website
     *
     * @return bool
     */
    public function isWebsiteScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_VENDOR_ACCOUNT_SHARE) == self::SHARE_WEBSITE;
    }

    /**
     * Get possible sharing configuration options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::SHARE_GLOBAL  => Mage::helper('vendor')->__('Global'),
            self::SHARE_WEBSITE => Mage::helper('vendor')->__('Per Website'),
        );
    }

    /**
     * Check for email dublicates before saving vendors sharing options
     *
     * @return Mage_vendor_Model_Config_Share
     * @throws Mage_Core_Exception
     */
    public function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == self::SHARE_GLOBAL) {
            if (Mage::getResourceSingleton('vendor/vendor')->findEmailDuplicates()) {
                Mage::throwException(
                    Mage::helper('vendor')->__('Cannot share vendor accounts globally because some vendor accounts with the same emails exist on multiple websites and cannot be merged.')
                );
            }
        }
        return $this;
    }
}

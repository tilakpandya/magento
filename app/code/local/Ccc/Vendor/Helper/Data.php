<?php

class Ccc_Vendor_Helper_Data extends Mage_Core_Helper_Abstract
{

    const REFERER_QUERY_PARAM_NAME = 'referer';

    const ROUTE_ACCOUNT_LOGIN = 'vendor/account/login';

    const XML_PATH_VENDOR_STARTUP_REDIRECT_TO_DASHBOARD = 'vendor/startup/redirect_dashboard';

    const XML_PATH_VENDOR_VIV_GROUP_AUTO_ASSIGN = 'vendor/create_account/viv_disable_auto_group_assign_default';

    const XML_PATH_SUPPORT_EMAIL = 'trans_email/ident_support/email';

    const VAT_VALIDATION_WSDL_URL = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService?wsdl';

    const XML_PATH_VENDOR_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
    = 'default/vendor/password/reset_link_expiration_period';

    const XML_PATH_VENDOR_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD
    = 'vendor/password/require_admin_user_to_change_user_password';

    const XML_PATH_VENDOR_FORGOT_PASSWORD_FLOW_SECURE = 'admin/security/forgot_password_flow_secure';
    const XML_PATH_VENDOR_FORGOT_PASSWORD_EMAIL_TIMES = 'admin/security/forgot_password_email_times';
    const XML_PATH_VENDOR_FORGOT_PASSWORD_IP_TIMES = 'admin/security/forgot_password_ip_times';

    const VAT_CLASS_DOMESTIC = 'domestic';
    const VAT_CLASS_INTRA_UNION = 'intra_union';
    const VAT_CLASS_INVALID = 'invalid';
    const VAT_CLASS_ERROR = 'error';

    protected $_groups;

    public function isLoggedIn()
    {
        return Mage::getSingleton('vendor/session')->isLoggedIn();
    }

    public function getVendor()
    {
        if (empty($this->_vendor)) {
            $this->_vendor = Mage::getSingleton('vendor/session')->getVendor();
        }
        return $this->_vendor;
    }

    public function getGroups()
    {
        if (empty($this->_groups)) {
            $this->_groups = Mage::getModel('vendor/group')->getResourceCollection()
                ->setRealGroupsFilter()
                ->load();
        }
        return $this->_groups;
    }

    public function getCurrentVendor()
    {
        return $this->getVendor();
    }

    public function getFullVendorName($object = null)
    {
        $name = '';
        if (is_null($object)) {
            $name = $this->getVendorName();
        } else {
            $config = Mage::getSingleton('eav/config');

            if (
                $config->getAttribute('vendor', 'prefix')->getIsVisible()
                && (
                    $object->getPrefix()
                    || $object->getVendorPrefix()
                )
            ) {
                $name .= ($object->getPrefix() ? $object->getPrefix() : $object->getVendorPrefix()) . ' ';
            }

            $name .= $object->getFirstname() ? $object->getFirstname() : $object->getVendorFirstname();

            if ($config->getAttribute('vendor', 'middlename')->getIsVisible()
                && (
                    $object->getMiddlename()
                    || $object->getVendorMiddlename()
                )
            ) {
                $name .= ' ' . (
                    $object->getMiddlename()
                    ? $object->getMiddlename()
                    : $object->getVendorMiddlename()
                );
            }

            $name .= ' ' . (
                $object->getLastname()
                ? $object->getLastname()
                : $object->getVendorLastname()
            );

            if ($config->getAttribute('vendor', 'suffix')->getIsVisible()
                && (
                    $object->getSuffix()
                    || $object->getVendorSuffix()
                )
            ) {
                $name .= ' ' . (
                    $object->getSuffix()
                    ? $object->getSuffix()
                    : $object->getVendorSuffix()
                );
            }
        }
        return $name;
    }


    public function getVendorName()
    {
        return $this->getVendor()->getName();
    }


    public function vendorHasAddresses()
    {
        return count($this->getVendor()->getAddresses()) > 0;
    }


    public function getLoginUrl()
    {
        return $this->_getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }

    public function getLoginUrlParams()
    {
        $params = array();

        $referer = $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME);

        if (!$referer && !Mage::getStoreConfigFlag(self::XML_PATH_VENDOR_STARTUP_REDIRECT_TO_DASHBOARD)
            && !Mage::getSingleton('vendor/session')->getNoReferer()
        ) {
            $referer = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
            $referer = Mage::helper('core')->urlEncode($referer);
        }

        if ($referer) {
            $params = array(self::REFERER_QUERY_PARAM_NAME => $referer);
        }

        return $params;
    }

    public function getLoginPostUrl()
    {
        $params = array();
        if ($this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = array(
                self::REFERER_QUERY_PARAM_NAME => $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME),
            );
        }
        return $this->_getUrl('vendor/account/loginPost', $params);
    }

    public function getLogoutUrl()
    {
        return $this->_getUrl('vendor/account/logout');
    }

    public function getDashboardUrl()
    {
        return $this->_getUrl('vendor/account');
    }

    public function getAccountUrl()
    {
        return $this->_getUrl('vendor/account/login');
    }

    public function getRegisterUrl()
    {
        return $this->_getUrl('vendor/account/create');
    }

    public function getRegisterPostUrl()
    {
        return $this->_getUrl('vendor/account/createpost');
    }

    public function getEditUrl()
    {
        return $this->_getUrl('vendor/account/edit');
    }

    public function getEditPostUrl()
    {
        return $this->_getUrl('vendor/account/editpost');
    }

    public function getForgotPasswordUrl()
    {
        return $this->_getUrl('vendor/account/forgotpassword');
    }

    public function isConfirmationRequired()
    {
        return $this->getVendor()->isConfirmationRequired();
    }

    public function getEmailConfirmationUrl($email = null)
    {
        return $this->_getUrl('vendor/account/confirmation', array('email' => $email));
    }

    public function isRegistrationAllowed()
    {
        $result = new Varien_Object(array('is_allowed' => true));
        Mage::dispatchEvent('vendor_registration_is_allowed', array('result' => $result));
        return $result->getIsAllowed();
    }


    public function getNamePrefixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions(
            Mage::helper('vendor/address')->getConfig('prefix_options', $store)
        );
    }

 
    public function getNameSuffixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions(
            Mage::helper('vendor/address')->getConfig('suffix_options', $store)
        );
    }


    protected function _prepareNamePrefixSuffixOptions($options)
    {
        $options = trim($options);
        if (empty($options)) {
            return false;
        }
        $result = array();
        $options = explode(';', $options);
        foreach ($options as $value) {
            $value = $this->escapeHtml(trim($value));
            $result[$value] = $value;
        }
        return $result;
    }

    public function generateResetPasswordLinkToken()
    {
        return Mage::helper('core')->uniqHash();
    }


    public function getResetPasswordLinkExpirationPeriod()
    {
        return (int) Mage::getConfig()->getNode(self::XML_PATH_VENDOR_RESET_PASSWORD_LINK_EXPIRATION_PERIOD);
    }

    public function getIsRequireAdminUserToChangeUserPassword()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_VENDOR_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD);
    }


    public function getDefaultVendorGroupId($store = null)
    {
        return (int) Mage::getStoreConfig(Mage_Vendor_Model_Group::XML_PATH_DEFAULT_ID, $store);
    }

 
    public function getVendorForgotPasswordFlowSecure()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_VENDOR_FORGOT_PASSWORD_FLOW_SECURE);
    }

    public function getVendorForgotPasswordEmailTimes()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_VENDOR_FORGOT_PASSWORD_EMAIL_TIMES);
    }

    /**
     * Retrieve forgot password requests to times per hour from 1 IP
     *
     * @return int
     */
    public function getVendorForgotPasswordIpTimes()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_VENDOR_FORGOT_PASSWORD_IP_TIMES);
    }

    /**
     * Retrieve vendor group ID based on his VAT number
     *
     * @param string $customerCountryCode
     * @param Varien_Object $vatValidationResult
     * @param Mage_Core_Model_Store|string|int $store
     * @return null|int
     */
    public function getVendorGroupIdBasedOnVatNumber($customerCountryCode, $vatValidationResult, $store = null)
    {
        $groupId = null;

        $vatClass = $this->getVendorVatClass($customerCountryCode, $vatValidationResult, $store);

        $vatClassToGroupXmlPathMap = array(
            self::VAT_CLASS_DOMESTIC => self::XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP,
            self::VAT_CLASS_INTRA_UNION => self::XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP,
            self::VAT_CLASS_INVALID => self::XML_PATH_CUSTOMER_VIV_INVALID_GROUP,
            self::VAT_CLASS_ERROR => self::XML_PATH_CUSTOMER_VIV_ERROR_GROUP,
        );

        if (isset($vatClassToGroupXmlPathMap[$vatClass])) {
            $groupId = (int) Mage::getStoreConfig($vatClassToGroupXmlPathMap[$vatClass], $store);
        }

        return $groupId;
    }

    /**
     * Send request to VAT validation service and return validation result
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     *
     * @return Varien_Object
     */
    public function checkVatNumber($countryCode, $vatNumber, $requesterCountryCode = '', $requesterVatNumber = '')
    {
        // Default response
        $gatewayResponse = new Varien_Object(array(
            'is_valid' => false,
            'request_date' => '',
            'request_identifier' => '',
            'request_success' => false,
        ));

        if (!extension_loaded('soap')) {
            Mage::logException(Mage::exception('Mage_Core',
                Mage::helper('core')->__('PHP SOAP extension is required.')));
            return $gatewayResponse;
        }

        if (!$this->canCheckVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber)) {
            return $gatewayResponse;
        }

        try {
            $soapClient = $this->_createVatNumberValidationSoapClient();

            $requestParams = array();
            $requestParams['countryCode'] = $countryCode;
            $requestParams['vatNumber'] = str_replace(array(' ', '-'), array('', ''), $vatNumber);
            $requestParams['requesterCountryCode'] = $requesterCountryCode;
            $requestParams['requesterVatNumber'] = str_replace(array(' ', '-'), array('', ''), $requesterVatNumber);

            // Send request to service
            $result = $soapClient->checkVatApprox($requestParams);

            $gatewayResponse->setIsValid((boolean) $result->valid);
            $gatewayResponse->setRequestDate((string) $result->requestDate);
            $gatewayResponse->setRequestIdentifier((string) $result->requestIdentifier);
            $gatewayResponse->setRequestSuccess(true);
        } catch (Exception $exception) {
            $gatewayResponse->setIsValid(false);
            $gatewayResponse->setRequestDate('');
            $gatewayResponse->setRequestIdentifier('');
        }

        return $gatewayResponse;
    }

    /**
     * Check if parameters are valid to send to VAT validation service
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     *
     * @return boolean
     */
    public function canCheckVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber)
    {
        $result = true;
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('core');

        if (!is_string($countryCode)
            || !is_string($vatNumber)
            || !is_string($requesterCountryCode)
            || !is_string($requesterVatNumber)
            || empty($countryCode)
            || !$coreHelper->isCountryInEU($countryCode)
            || empty($vatNumber)
            || (empty($requesterCountryCode) && !empty($requesterVatNumber))
            || (!empty($requesterCountryCode) && empty($requesterVatNumber))
            || (!empty($requesterCountryCode) && !$coreHelper->isCountryInEU($requesterCountryCode))
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get VAT class
     *
     * @param string $customerCountryCode
     * @param Varien_Object $vatValidationResult
     * @param Mage_Core_Model_Store|string|int|null $store
     * @return null|string
     */
    public function getVendorVatClass($customerCountryCode, $vatValidationResult, $store = null)
    {
        $vatClass = null;

        $isVatNumberValid = $vatValidationResult->getIsValid();

        if (is_string($customerCountryCode)
            && !empty($customerCountryCode)
            && $customerCountryCode === Mage::helper('core')->getMerchantCountryCode($store)
            && $isVatNumberValid
        ) {
            $vatClass = self::VAT_CLASS_DOMESTIC;
        } elseif ($isVatNumberValid) {
            $vatClass = self::VAT_CLASS_INTRA_UNION;
        } else {
            $vatClass = self::VAT_CLASS_INVALID;
        }

        if (!$vatValidationResult->getRequestSuccess()) {
            $vatClass = self::VAT_CLASS_ERROR;
        }

        return $vatClass;
    }

    /**
     * Get validation message that will be displayed to user by VAT validation result object
     *
     * @param Mage_Customer_Model_Address $customerAddress
     * @param bool $customerGroupAutoAssignDisabled
     * @param Varien_Object $validationResult
     * @return Varien_Object
     */
    public function getVatValidationUserMessage($customerAddress, $customerGroupAutoAssignDisabled, $validationResult)
    {
        $message = '';
        $isError = true;
        $customerVatClass = $this->getCustomerVatClass($customerAddress->getCountryId(), $validationResult);
        $groupAutoAssignDisabled = Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_VIV_GROUP_AUTO_ASSIGN);

        $willChargeTaxMessage = $this->__('You will be charged tax.');
        $willNotChargeTaxMessage = $this->__('You will not be charged tax.');

        if ($validationResult->getIsValid()) {
            $message = $this->__('Your VAT ID was successfully validated.');
            $isError = false;

            if (!$groupAutoAssignDisabled && !$customerGroupAutoAssignDisabled) {
                $message .= ' ' . ($customerVatClass == self::VAT_CLASS_DOMESTIC
                    ? $willChargeTaxMessage
                    : $willNotChargeTaxMessage);
            }
        } else if ($validationResult->getRequestSuccess()) {
            $message = sprintf(
                $this->__('The VAT ID entered (%s) is not a valid VAT ID.') . ' ',
                $this->escapeHtml($customerAddress->getVatId())
            );
            if (!$groupAutoAssignDisabled && !$customerGroupAutoAssignDisabled) {
                $message .= $willChargeTaxMessage;
            }
        } else {
            $contactUsMessage = sprintf($this->__('If you believe this is an error, please contact us at %s'),
                Mage::getStoreConfig(self::XML_PATH_SUPPORT_EMAIL));

            $message = $this->__('Your Tax ID cannot be validated.') . ' '
                . (!$groupAutoAssignDisabled && !$customerGroupAutoAssignDisabled
                ? $willChargeTaxMessage . ' ' : '')
                . $contactUsMessage;
        }

        $validationMessageEnvelope = new Varien_Object();
        $validationMessageEnvelope->setMessage($message);
        $validationMessageEnvelope->setIsError($isError);

        return $validationMessageEnvelope;
    }

    /**
     * Create SOAP client based on VAT validation service WSDL
     *
     * @param boolean $trace
     * @return SoapClient
     */
    protected function _createVatNumberValidationSoapClient($trace = false)
    {
        return new SoapClient(self::VAT_VALIDATION_WSDL_URL, array('trace' => $trace));
    }
}

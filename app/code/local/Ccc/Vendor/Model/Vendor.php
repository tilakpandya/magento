<?php
class Ccc_Vendor_Model_Vendor extends Mage_Core_Model_Abstract
{

    const ENTITY = 'vendor';

    const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'vendor/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'vendor/create_account/email_identity';
    const XML_PATH_REMIND_EMAIL_TEMPLATE = 'vendor/password/remind_email_template';
    const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'vendor/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY = 'vendor/password/forgot_email_identity';
    const XML_PATH_DEFAULT_EMAIL_DOMAIN = 'vendor/create_account/email_domain';
    const XML_PATH_IS_CONFIRM = 'vendor/create_account/confirm';
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE = 'vendor/create_account/email_confirmation_template';
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE = 'vendor/create_account/email_confirmed_template';
    const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID = 'vendor/create_account/generate_human_friendly_id';
    const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE = 'vendor/changed_account/password_or_email_template';
    const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY = 'vendor/changed_account/password_or_email_identity';

    const EXCEPTION_EMAIL_NOT_CONFIRMED = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN = 4;
    const EXCEPTION_EMAIL_EXISTS = 3;

    const CACHE_TAG = 'vendor';

    const MINIMUM_PASSWORD_LENGTH = 6;

    protected $_eventPrefix = 'vendor';

    protected $_eventObject = 'vendor';

    protected $_errors = array();

    protected $_addresses = null;

    protected $_attributes;

    protected $_addressesCollection;

    protected $_isDeleteable = true;

    protected $_isReadonly = false;

    protected $_cacheTag = self::CACHE_TAG;

    private static $_isConfirmationRequired;

    public function getSharingConfig()
    {
        return Mage::getSingleton('vendor/config_share');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('vendor/vendor');

    }

    public function getAttributes()
    {

        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()
                ->loadAllAttributes($this)
                ->getSortedAttributes();
        }
        return $this->_attributes;
    }
    public function checkInGroup($attributeId, $setId, $groupId)
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');
        $readConnection = $resource->getConnection('core_read');

        $query = '
            SELECT * FROM ' .
        $resource->getTableName('eav/entity_attribute')
            . ' WHERE `attribute_id` =' . $attributeId
            . ' AND `attribute_group_id` =' . $groupId
            . ' AND `attribute_set_id` =' . $setId
        ;

        $results = $readConnection->fetchRow($query);

        if ($results) {
            return true;
        }
        return false;
    }
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('vendor')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('vendor')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }
        Mage::dispatchEvent('vendor_vendor_authenticated', array(
            'model' => $this,
            'password' => $password,
        ));

        return true;
    }

    public function loadByEmail($vendorEmail)
    {
        
        $this->_getResource()->loadByEmail($this, $vendorEmail);
        return $this;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        $storeId = $this->getStoreId();
        if ($storeId === null) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->getGroupId();
        return $this;
    }

    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$attributeCode])) {
            return $this->_attributes[$attributeCode];
        }
        return null;
    }

    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        $this->setPasswordConfirmation(null);
        return $this;
    }

    public function hashPassword($password, $salt = null)
    {
        return $this->_getHelper('core')
            ->getHash(trim($password), !is_null($salt) ? $salt : Mage_Admin_Model_User::HASH_SALT_LENGTH);
    }

    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
        . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
        . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
        . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }

    public function validatePassword($password)
    {
        $hash = $this->getPasswordHash();
        if (!$hash) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }

    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }
    public function canSkipConfirmation()
    {
        return $this->getId() && $this->hasSkipConfirmationIfEmail()
        && strtolower($this->getSkipConfirmationIfEmail()) === strtolower($this->getEmail());
    }
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0', $password = null)
    {
        $types = array(
            'registered' => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed' => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('vendor')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        if (!is_null($password)) {
            $this->setPassword($password);
        }

        $this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('vendor' => $this, 'back_url' => $backUrl), $storeId);
        $this->cleanPasswordsValidationData();

        return $this;
    }

    public function isConfirmationRequired()
    {
        if ($this->canSkipConfirmation()) {
            return false;
        }
        if (self::$_isConfirmationRequired === null) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : null;
            self::$_isConfirmationRequired = (bool) Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM, $storeId);
        }

        return self::$_isConfirmationRequired;
    }

    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    public function sendPasswordReminderEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(self::XML_PATH_REMIND_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('vendor' => $this), $storeId);

        return $this;
    }

    public function sendChangedPasswordOrEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE,
            self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY,
            array('vendor' => $this), $storeId, $this->getOldEmail());

        return $this;
    }

    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null, $vendorEmail = null)
    {
        // $vendorEmail = ($vendorEmail) ? $vendorEmail : $this->getEmail();
        // /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        // $mailer = Mage::getModel('core/email_template_mailer');
        // $emailInfo = Mage::getModel('core/email_info');
        // $emailInfo->addTo($vendorEmail, $this->getName());
        // $mailer->addEmailInfo($emailInfo);

        // // Set all required params and send emails
        // $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        // $mailer->setStoreId($storeId);
        // $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        // $mailer->setTemplateParams($templateParams);
        // $mailer->send();
        // return $this;
    }

    public function sendPasswordResetConfirmationEmail()
    {
        $storeId = Mage::app()->getStore()->getId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(self::XML_PATH_FORGOT_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('vendor' => $this), $storeId);

        return $this;
    }

    public function getTaxClassId()
    {
        if (!$this->getData('tax_class_id')) {
            $this->setTaxClassId(Mage::getModel('vendor/group')->getTaxClassId($this->getGroupId()));
        }
        return $this->getData('tax_class_id');
    }

    public function isInStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = $store->getId();
        } else {
            $storeId = $store;
        }

        $availableStores = $this->getSharedStoreIds();
        return in_array($storeId, $availableStores);
    }

    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if ($ids === null) {
            $ids = array();
            if ((bool) $this->getSharingConfig()->isWebsiteScope()) {
                $ids = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            } else {
                foreach (Mage::app()->getStores() as $store) {
                    $ids[] = $store->getId();
                }
            }
            $this->setData('shared_store_ids', $ids);
        }

        return $ids;
    }

    public function getSharedWebsiteIds()
    {
        $ids = $this->_getData('shared_website_ids');
        if ($ids === null) {
            $ids = array();
            if ((bool) $this->getSharingConfig()->isWebsiteScope()) {
                $ids[] = $this->getWebsiteId();
            } else {
                foreach (Mage::app()->getWebsites() as $website) {
                    $ids[] = $website->getId();
                }
            }
            $this->setData('shared_website_ids', $ids);
        }
        return $ids;
    }

    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        $this->setWebsiteId($store->getWebsite()->getId());
        return $this;
    }

    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is(trim($this->getFirstname()), 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is(trim($this->getLastname()), 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('vendor')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(self::MINIMUM_PASSWORD_LENGTH))) {
            $errors[] = Mage::helper('vendor')
                ->__('The minimum password length is %s', self::MINIMUM_PASSWORD_LENGTH);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('vendor')->__('Please make sure your passwords match.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    public function validateResetPassword()
    {
        $errors = array();
        $password = $this->getPassword();
        if (!Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The password cannot be empty.');
        }
        if (!Zend_Validate::is($password, 'StringLength', array(self::MINIMUM_PASSWORD_LENGTH))) {
            $errors[] = Mage::helper('vendor')
                ->__('The minimum password length is %s', self::MINIMUM_PASSWORD_LENGTH);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('vendor')->__('Please make sure your passwords match.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return Varien_Date::toTimestamp($date);
        }
        return null;
    }

    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }

    protected function _getWebsiteStoreId($defaultStoreId = null)
    {
        if ($this->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    public function changeResetPasswordLinkToken($newResetPasswordLinkToken)
    {
        if (!is_string($newResetPasswordLinkToken) || empty($newResetPasswordLinkToken)) {
            throw Mage::exception('Mage_Core', Mage::helper('vendor')->__('Invalid password reset token.'),
                self::EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN);
        }
        $this->_getResource()->changeResetPasswordLinkToken($this, $newResetPasswordLinkToken);
        return $this;
    }

    public function isResetPasswordLinkTokenExpired()
    {
        $resetPasswordLinkToken = $this->getRpToken();
        $resetPasswordLinkTokenCreatedAt = $this->getRpTokenCreatedAt();

        if (empty($resetPasswordLinkToken) || empty($resetPasswordLinkTokenCreatedAt)) {
            return true;
        }

        $tokenExpirationPeriod = Mage::helper('vendor')->getResetPasswordLinkExpirationPeriod();

        $currentDate = Varien_Date::now();
        $currentTimestamp = Varien_Date::toTimestamp($currentDate);
        $tokenTimestamp = Varien_Date::toTimestamp($resetPasswordLinkTokenCreatedAt);
        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }

        $hoursDifference = floor(($currentTimestamp - $tokenTimestamp) / (60 * 60));
        if ($hoursDifference >= $tokenExpirationPeriod) {
            return true;
        }

        return false;
    }

    public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('password_confirmation', null);
        return $this;
    }

}

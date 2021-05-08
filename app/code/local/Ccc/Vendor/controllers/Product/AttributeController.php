<?php

class Ccc_Vendor_Product_AttributeController extends Mage_Core_Controller_Front_Action
{
    protected $_entityTypeId;

    public function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType(Ccc_Vendor_Model_Resource_Product::ENTITY)->getTypeId();
    }

    public function indexAction()
    {
      
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/account/login');
        }
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Attribute '));
        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        try {
            $id = (int) $this->getRequest()->getParam('attribute_id');
            $session = $this->_getSession();
            $model = Mage::getModel('vendor/resource_eav_attribute')->setEntityTypeId($this->_entityTypeId);

            // if ($id != !$model->load($id)->getId()) {
            //     throw new Exception("Invalid attribute id");
            // }
            if ($id && !$model->load($id)->getId()) {
                throw new Exception("Invalid attribute id");
            }

            if ($id) {
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                        Mage::helper('vendor')->__('This attribute cannot be edited.'));
                    $this->_redirect('*/*/');
                    return;
                }
            }
            Mage::register('entity_attribute', $model);
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Edit Attribute'));
            $this->_initLayoutMessages('vendor/session');
            $this->renderLayout();
        } catch (Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirect('*/*/index');
        }
    }

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attribute = Mage::getModel('vendor/resource_eav_attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('vendor/session')->addError(
                Mage::helper('vendor')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('vendor/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    } 

    protected function _filterPostData($data)
    {
        if ($data) {
            
            $helperCatalog = Mage::helper('vendor');
            echo "<pre>";
            $data['frontend_label'] = (array) $data['frontend_label'];
            foreach ($data['frontend_label'] as &$value) {
                if ($value) {
                    $value = $helperCatalog->stripTags($value);
                    
                }
                
            }
 
            if (!empty($data['option']) && !empty($data['option']['value']) && is_array($data['option']['value'])) {
                $allowableTags = isset($data['is_html_allowed_on_front']) && $data['is_html_allowed_on_front']
                ? sprintf('<%s>', implode('><', $this->_getAllowedTags())) : null;
                foreach ($data['option']['value'] as $key => $values) {
                    foreach ($values as $storeId => $storeLabel) {
                        $data['option']['value'][$key][$storeId]
                        = $helperCatalog->stripTags($storeLabel, $allowableTags);
                    }
                }
            }
        }
        
           
        return $data;
    }

    public function saveAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
        }
        $data = $this->getRequest()->getPost();
        
        
        $nameArray = explode(" ", $data['frontend_label'][0]);
        $array = [];
        
        foreach ($nameArray as $name) {
            $array[] = strtolower($name);
        }
        
        $data['attribute_code'] = implode("_", $array);
        
        if ($data) {

            $session = Mage::getSingleton('vendor/session');

            $redirectBack = $this->getRequest()->getParam('back', false);

            $model = Mage::getModel('vendor/resource_eav_attribute');

            $helper = Mage::helper('vendor/vendor');

            $id = $this->getRequest()->getParam('attribute_id');
            
            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^(?!event$)[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    $session->addError(
                        Mage::helper('vendor')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter. Do not use "event" for an attribute code.')
                    );
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }
            
            //validate frontend_input
            if (isset($data['frontend_input'])) {
                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        $session->addError($message);
                    }
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }
            
            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                        Mage::helper('vendor')->__('This Attribute no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                        Mage::helper('vendor')->__('This attribute cannot be updated.'));
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }
                
                $data['backend_model'] = $model->getBackendModel();
                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            } else {
                $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
            }
            
            if (!isset($data['is_configurable'])) {
                $data['is_configurable'] = 0;
            }
            if (!isset($data['is_filterable'])) {
                $data['is_filterable'] = 0;
            }
            if (!isset($data['is_filterable_in_search'])) {
                $data['is_filterable_in_search'] = 0;
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }
            
            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }

            //filter
            $data = $this->_filterPostData($data);
            $model->addData($data);
            
            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }
           
            if ($this->getRequest()->getParam('set') && $this->getRequest()->getParam('group')) {
                // For creating product attribute on product page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId($this->getRequest()->getParam('group'));
               
                
            }
            /* echo "<pre>";
            print_r( $model); */
            try {
                if (!$id) {
                    $model->setAttributeCode($session->getId() . '_' . $model->getAttributeCode() . '_' . $session->getId());
                }
                
                $model->save();
                
                $eavEntityAttribute = Mage::getModel('eav/entity_attribute');
                $vendorProduct = Mage::getModel('vendor/product');
                $defaultAttributeSetId = $vendorProduct->getResource()->getEntityType()->getDefaultAttributeSetId();

                $eavEntityAttribute->setEntityTypeId($model->getEntityTypeId());
                $eavEntityAttribute->setAttributeId($model->getAttributeId());
                $eavEntityAttribute->setAttributeGroupId($this->getRequest()->getParam('group'));
                $eavEntityAttribute->setAttributeSetId($defaultAttributeSetId);
                
                $eavEntityAttribute->save();
               
                $session->addSuccess(
                    Mage::helper('vendor')->__('The Vendor attribute has been saved.'));

                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setAttributeData(false);
               
                $this->_redirect('*/*/', array());

                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                return;
            }
        }
        
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('attribute_id')) {
            $model = Mage::getModel('vendor/resource_eav_attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId || !$model->getIsUserDefined()) {
                Mage::getSingleton('vendor/session')->addError(
                    Mage::helper('vendor')->__('This attribute cannot be deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            try {
                $model->delete();
                Mage::getSingleton('vendor/session')->addSuccess(
                    Mage::helper('vendor')->__('The Product attribute has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('vendor/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('vendor/session')->addError(
            Mage::helper('vendor')->__('Unable to find an attribute to delete.'));
        $this->_redirect('*/*/');
    }
}

?>
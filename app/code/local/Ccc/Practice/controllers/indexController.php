<?php 

class Ccc_Practice_indexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        /* $block = $this->getLayout()->createBlock('practice/user');
        $this->getLayout()->getBlock('content')->insert($block); */
        //$this->_addContent($this->getLayout()->createBlock('practice/practice'));
        $this->renderLayout();
    }
}

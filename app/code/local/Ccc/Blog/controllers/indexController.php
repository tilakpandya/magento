<?php

class Ccc_Blog_IndexController
    extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        
        $post = Mage::getModel('blog/blog');
        $post->setTitle('Test title');
        $post->setAuthor('Zoran Šalamun');
        $post->save();

        
        $post = Mage::getModel('blog/blog');
        $post->setBookType('Test title');
        $post->save();

       
        $post = Mage::getModel('blog/blog')->getCollection();
        $posts->load();
        var_dump($posts);

        
        $posts = Mage::getModel('blog/blog')->getCollection()
                ->addAttributeToSelect('title')
                ->addAttributeToSelect('author');
        $posts->load();
        var_dump($posts);

        
        $post = Mage::getModel('blog/blog')->load(1);
        var_dump($post);
    }
}
<?php
class Easylife_News_Model_Observer {
    public function addItemsToTopmenuItems($observer) {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();

        $articleNodeId = 'article';
        $data = array(
            'name' => Mage::helper('easylife_news')->__('Articles'),
            'id' => $articleNodeId,
            'url' => Mage::helper('easylife_news/article')->getArticlesUrl(),
            'is_active' => ($action == 'easylife_news_article_index' || $action == 'easylife_news_article_view')
        );
        $articleNode = new Varien_Data_Tree_Node($data, 'id', $tree, $menu);
        $menu->addChild($articleNode);
        return $this;
    }
}
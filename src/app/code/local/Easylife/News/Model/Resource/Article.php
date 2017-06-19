<?php
class Easylife_News_Model_Resource_Article
    extends Mage_Catalog_Model_Resource_Abstract {
    public function __construct() {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('easylife_news_article')
            ->setConnection(
                $resource->getConnection('article_read'),
                $resource->getConnection('article_write')
            );

    }
    public function getMainTable() {
        return $this->getEntityTable();
    }
    public function checkUrlKey($urlKey, $storeId, $active = true){
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initCheckUrlKeySelect($urlKey, $stores);
        if (!$select){
            return false;
        }
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);
        return $this->_getReadAdapter()->fetchOne($select);
    }
    protected function _initCheckUrlKeySelect($urlKey, $store){
        $urlRewrite = Mage::getModel('eav/config')->getAttribute('easylife_news_article', 'url_key');
        if (!$urlRewrite || !$urlRewrite->getId()){
            return false;
        }
        $table = $urlRewrite->getBackend()->getTable();
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $table))
            ->where('e.attribute_id = ?', $urlRewrite->getId())
            ->where('e.value = ?', $urlKey)
            ->where('e.store_id IN (?)', $store)
            ->order('e.store_id DESC');
        return $select;
    }
}
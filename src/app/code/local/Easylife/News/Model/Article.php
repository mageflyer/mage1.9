<?php
class Easylife_News_Model_Article
    extends Mage_Catalog_Model_Abstract {
    const ENTITY    = 'easylife_news_article';
    const CACHE_TAG = 'easylife_news_article';
    protected $_eventPrefix = 'easylife_news_article';
    protected $_eventObject = 'article';
    public function _construct(){
        parent::_construct();
        $this->_init('easylife_news/article');
    }
    protected function _beforeSave(){
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }
    public function getArticleUrl(){
        if ($this->getUrlKey()){
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('easylife_news/article/url_prefix')){
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('easylife_news/article/url_suffix')){
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('easylife_news/article/view', array('id'=>$this->getId()));
    }
    public function checkUrlKey($urlKey, $active = true){
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    public function getDescription(){ //this needs to be implemented for all WYSIWYG attributes
        $description = $this->getData('description');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($description);
        return $html;
    }
    public function getDefaultAttributeSetId() {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }
    public function getAttributeText($attributeCode) {
        $text = $this->getResource()
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getData($attributeCode));
        if (is_array($text)){
            return implode(', ',$text);
        }
        return $text;
    }
    public function getDefaultValues() {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        return $values;
    }
}
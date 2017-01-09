<?php

class EcomCore_M2ext_Model_Observer_Collection
{

    public $debug = false;

    public function onEavLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) return;

        if ($this->debug) Mage::log(__METHOD__.'() Collection class: '.get_class($collection));

        $columns = $collection->getSelect()->getPart('columns');
        foreach ($columns as $column) {
            if ($column[0] == 'elp' && $column[1] == 'ebay_item_id') {
                return;
            }
        }

        if ($collection instanceOf Mage_Catalog_Model_Resource_Product_Collection
            && false === ($collection instanceOf Ess_M2ePro_Model_Mysql4_Magento_Product_Collection)
        ) {

            $res = Mage::getSingleton('core/resource');
            $collection->joinTable(
                array('m2elp' => $res->getTableName('m2epro_listing_product')),
                'product_id=entity_id',
                array('m2e_listing_product_id' => 'id'),
                array('status' => array(2,7)),
                'left'
            );

            $collection->joinTable(
                array('m2eelp' => $res->getTableName('m2epro_ebay_listing_product')),
                'listing_product_id=m2e_listing_product_id',
                array('ebay_item_id' => 'ebay_item_id'),
                null,
                'left'
            );

            $collection->joinTable(
                array('m2eitem' => $res->getTableName('m2epro_ebay_item')),
                'id=ebay_item_id',
                array('ebayitemid' => 'GROUP_CONCAT(m2eitem.item_id)'),
                null,
                'left'
            );
            $collection->addFilterToMap('ebayitemid', 'm2eitem.item_id');

            $collection->getSelect()->group('e.entity_id');
            if ($this->debug) Mage::log(__METHOD__.'() '.$collection->getSelect());

        }
    }

}

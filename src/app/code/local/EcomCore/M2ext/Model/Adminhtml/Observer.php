<?php

class EcomCore_M2ext_Model_Adminhtml_Observer
{

    public function onBlockHtmlBefore(Varien_Event_Observer $observer) {
        $block = $observer->getBlock();
        if (!isset($block)) return;

        switch ($block->getType()) {
            case 'adminhtml/catalog_product_grid':
                $block->addColumn('ebayitemid', array(
                    'header' => Mage::helper('eccm2ext')->__('eBay Item ID'),
                    'index'  => 'ebayitemid',
                ));
                break;
        }
    }

    public function onEavLoadBefore(Varien_Event_Observer $observer) {
        $collection = $observer->getCollection();
        if (!isset($collection)) return;

        if (is_a($collection, 'Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection')) {

            $res = Mage::getSingleton('core/resource');
            $collection->joinTable(
                array('m2elp' => $res->getTableName('m2epro_listing_product')),
                'product_id=entity_id',
                array('m2e_listing_product_id' => 'id'),
                null,
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

            $collection->getSelect()->group('entity_id');
        }
    }

}

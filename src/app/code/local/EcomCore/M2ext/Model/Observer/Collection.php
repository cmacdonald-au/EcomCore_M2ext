<?php

class EcomCore_M2ext_Model_Observer_Collection
{

    public function onEavLoadBefore(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) return;

        if (get_class($collection) == 'Mage_Catalog_Model_Resource_Product_Collection') {

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
            $collection->addFilterToMap('ebayitemid', 'm2eitem.item_id');

            $collection->getSelect()->group('e.entity_id');

        }
    }

}

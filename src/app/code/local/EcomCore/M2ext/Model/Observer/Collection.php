<?php

class EcomCore_M2ext_Model_Observer_Collection
{

<<<<<<< HEAD:src/app/code/local/EcomCore/M2ext/Model/Observer/Collection.php
    public function onEavLoadBefore(Varien_Event_Observer $observer) {
=======
    public function onBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if (!isset($block)) return;

        switch ($block->getType()) {
            case 'adminhtml/catalog_product_grid':
                $block->addColumnAfter('ebayitemid', array(
                    'header'   => Mage::helper('eccm2ext')->__('eBay Item ID'),
                    'index'    => 'ebayitemid',
                    'width'    => '100px',
                    'sortable' => false,
                    'filter_index' => 'm2eitem.item_id',
                    'frame_callback' => array($this, 'callbackColumnEbayItemId'),
                    'filter_condition_callback' => array($this, 'itemFilter'),
                ), 'sku');

                $block->sortColumnsByOrder();

                break;
        }
    }

    public function callbackColumnEbayItemId($value, $row, $column, $isExport)
    {
        if (is_null($value) || $value === '') {
            return '';
        }

        return '<a href="http://ebay.com.au/itm/-/' . $value . '" target="_blank">'.$value.'</a>';
    }

    public function itemFilter($collection, $column)
    {

        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where("$field=?", $value); // just as an example

    }

    public function onEavLoadBefore(Varien_Event_Observer $observer)
    {
>>>>>>> 461197b0b9a9bfab437b05e45505f0df594bb53b:src/app/code/local/EcomCore/M2ext/Model/Adminhtml/Observer.php
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

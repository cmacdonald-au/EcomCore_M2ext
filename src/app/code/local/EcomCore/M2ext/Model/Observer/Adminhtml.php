<?php

class EcomCore_M2ext_Model_Observer_Adminhtml
{

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

}
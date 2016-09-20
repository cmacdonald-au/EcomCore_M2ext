<?php

class EcomCore_M2ext_Model_Observer_Adminhtml
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

}
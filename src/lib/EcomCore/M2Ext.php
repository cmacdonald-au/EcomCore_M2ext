<?php

class EcomCore_M2Ext
{
    protected static $M2eOrder = null;
    protected static $lastOrderId = null;

    public static function isM2eOrder($order)
    {
        // if we have the order already or we've looked up this order id already
        if (self::$M2eOrder !== null && is_object(self::$M2eOrder)) {
            if (self::$M2eOrder === false && self::$lastOrderId == $order->getId()) {
                return false;
            }
            if (self::$M2eOrder->getMagentoOrderId() == $order->getId()) {
                return true;
            }
            if (self::$lastOrderId == $order->getId()) {
                return false;
            }
        }

        try {
            self::$lastOrderId = $order->getId();
            /** @var Ess_M2ePro_Model_Order $order */
            $m2order = Mage::helper('M2ePro/Component')->getUnknownObject(
               'Order', (int)$order->getId(), 'magento_order_id'
            );
        } catch (Exception $exception) {
            self::$M2eOrder = false;
            return false;
        }

        if (is_null($m2order) || !$m2order->getId()) {
            self::$M2eOrder = false;
            return false;
        }

        if (!Mage::helper('M2ePro/Component_'.ucfirst($m2order->getComponentMode()))->isActive()) {
            self::$M2eOrder = false;
            return false;
        }

        self::$M2eOrder = $m2order;
        return true;
    }

}
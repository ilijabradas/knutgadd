<?php   
class Pixelshop_Integration_Block_Index extends Mage_Core_Block_Template {

    public static $_store = '';

    protected function _getStore()
    {
        if(self::$_store){
            self::$_store = Mage::app()->getStore()->getStoreId();
        }

        return self::$_store;
    }

    public function getParameters()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();

        $order = Mage::getModel('sales/order')->load($orderId);

        $total = (float) $order->getSubtotal();

        $items = $order->getAllItems();

        $products = '';

        foreach($items as $i):
            $products .= $i->getProductId() . ',';
        endforeach;

        $products = substr($products, 0, -1);

        $currency = Mage::app()->getStore($this->_getStore())->getCurrentCurrencyCode();

        $data = array(
            "order_id"  => $orderId,
            "revenue"   => $total,
            "products"  => $products,
            "currency"  => $currency
        );

        return $data;
    }

    public function getPixelshopId()
    {
        return Mage::getStoreConfig('pixelshop/pixelshop/pixelshop_id', $this->_getStore());
    }

    public function getThePixel()
    {
        $params = $this->getParameters();

        return $this->getPixelshopId() . '?order_id=' . $params['order_id'] . '&amp;revenue=' . $params['revenue'] . '&amp;currency=' . $params['currency'] . '&amp;products=' . $params['products'] . '';
    }

}


<?php

require_once('./app/Mage.php');
umask(0);
Mage::app('default');

$order = Mage::getModel('sales/order')->loadByIncrementId('300000083');

$ordered_items = $order->getAllItems();

$line_items = array();

foreach ($ordered_items as $item) {
    $product = Mage::getModel('catalog/product')->load($item->getProductId());

    $compoundPrices  = $product->getCompoundPrices();
    $JPYProductPrice = '';
    foreach ($compoundPrices as $index => $price) {
        if ($index != 3) {
            continue;
        }
        $JPYProductPrice = $price['JPY'];
    }

    $line_item = new stdClass();
    $line_item->code = $item->getSku();
    $formattedPrice = number_format($JPYProductPrice, 0, '.', '');
    $line_item->price = (int)$formattedPrice;
    $formattedQty = number_format($item->getQtyOrdered(), 0);
    $line_item->quantity = (int)$formattedQty;

    $line_items[] = $line_item;

}
$formattedGrandTotal = number_format($order->getGrandTotal() - $order->getShippingAmount(), 0, '.', '');
$grandTotal = (int)$formattedGrandTotal;

 $result = array(
        "pid"=> "s00000019950001",
        "order_number" => $order->getIncrementId(),
        "currency" => Mage::app()->getStore()->getCurrentCurrencyCode(),
        "items" => json_encode($line_items),
        "total_price" =>  $grandTotal,
    );
Zend_Debug::dump($result, $label = null, $echo = true);
Zend_Debug::dump($line_items, $label = null, $echo = true);


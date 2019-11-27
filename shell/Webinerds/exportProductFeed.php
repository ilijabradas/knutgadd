<?php
define('SAVE_FEED_LOCATION','productfeed.txt');
set_time_limit(1800);
require_once (dirname(__FILE__).'/../../app/Mage.php');
Mage::app('default');
try{
    $handle = fopen(SAVE_FEED_LOCATION, 'w');

    $heading = array('ProductID','ProductName','ProductDescription','ProductURL','ImageURL','ProductPrice','ProductBrandName', 'ProductCategory');
    $feed_line=implode("\t", $heading)."\r\n";
    fwrite($handle, $feed_line);

    $products = Mage::getModel('catalog/product')->getCollection();
    $products->addAttributeToFilter('status', 1);
    $products->addAttributeToFilter('visibility', 4);
    $products->addAttributeToSelect('*');
    $prodIds=$products->getAllIds();

    $product = Mage::getModel('catalog/product');

    $counter_test = 0;

    foreach($prodIds as $productId) {

        if (++$counter_test < 30000){

            $product->load($productId);

            $product_data = array();
            $product_data['sku'] = $product->getSku();

            $title_temp = $product->getName();
            if (strlen($title_temp) > 70){
                $title_temp = str_replace("Supply", "", $title_temp);
                $title_temp = str_replace("  ", " ", $title_temp);
            }
            $product_data['title'] = $title_temp;


            $product_data['description'] = substr(iconv("UTF-8","UTF-8//IGNORE",$product->getDescription()), 0, 900);
            $product_data['Deeplink'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$product->getUrlPath();
            $product_data['image_link'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage();

            $price_temp = round($product->getPrice(),2);
            $product_data['price'] = round($product->getPrice(),2) + 5;

            $product_data['brand'] = $product->getData('brand');

//            $product_data['category'] = $product->getCategoryIds();
            $cats = $product->getCategoryIds();
            foreach ($cats as $category_id) {
                $_cat = Mage::getModel('catalog/category')->load($category_id) ;
                $product_data['category'] = $_cat->getName().' ';
            }

            foreach($product_data as $k=>$val){
                $bad=array('"',"\r\n","\n","\r","\t");
                $good=array(""," "," "," ","");
                $product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
            }

            echo $counter_test  . " ";

            $feed_line = implode("\t", $product_data)."\r\n";
            fwrite($handle, $feed_line);
            fflush($handle);

        }

    }

    fclose($handle);
}
catch(Exception $e){
    die($e->getMessage());
}
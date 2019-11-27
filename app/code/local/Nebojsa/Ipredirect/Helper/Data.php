<?php
/**
 * Created by PhpStorm.
 * User: WOLF
 * Date: 9/11/2018
 * Time: 10:23 AM
 */
require_once(Mage::getBaseDir('lib') . '/IP2Location/IP2Location.php');

class Nebojsa_Ipredirect_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getCountyCode() {
        $loc = new \IP2Location\Database(Mage::getBaseDir('lib') . '/IP2Location/databases/IP-COUNTRY-SAMPLE.BIN', \IP2Location\Database::FILE_IO);
        $record = $loc->lookup($_SERVER['REMOTE_ADDR'], \IP2Location\Database::ALL);

        return $record;

    }
    public function getLocationInfoByIp(){
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = @$_SERVER['REMOTE_ADDR'];
        $result  = array('country'=>'');
        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }else{
            $ip = $remote;
        }
        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
        if($ip_data && $ip_data->geoplugin_countryName != null){
            $result = $ip_data->geoplugin_countryCode;
        }
        return $result;
    }

}
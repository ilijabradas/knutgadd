<?php

class PI_Oopsprofile_ApiController extends Mage_Core_Controller_Front_Action {

		//Display Api responsce action
    public function indexAction()
    {
				try{
						$this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
					
						$request = $this->getRequest()->getParams();

						$baseUrl = Mage::getBaseUrl();

						$result = '';
				
						$client = new Zend_XmlRpc_Client($baseUrl.'api/xmlrpc/');

						//$client = new Zend_XmlRpc_Client('http://magento.PI.tv/extensions/magento-1.7.0.2/index.php/api/xmlrpc/');

						// If somestuff requires api authentification,
						// then get a session token
						$session = $client->call('login', array('OopsProfileUser', 'oopsprofileapikey'));

						if($request['entity']=='product')
						{
								$result = $client->call('call', array($session, 'oopsprofile.exportproductdata', array(array('profileid'=>$request['id']))));
						}
						else if($request['entity']=='customer')
						{
								$result = $client->call('call', array($session, 'oopsprofile.exportcustomerdata', array(array('profileid'=>$request['id']))));
						}
						else if($request['entity']=='customergroup')
						{
								$result = $client->call('call', array($session, 'oopsprofile.exportcustomergroupdata', array(array('profileid'=>$request['id']))));
						}
						else if($request['entity']=='order')
						{
								$result = $client->call('call', array($session, 'oopsprofile.exportorderdata', array(array('profileid'=>$request['id']))));
						}
						else if($request['entity']=='invoice')
						{
								$result = $client->call('call', array($session, 'oopsprofile.exportinvoicedata', array(array('profileid'=>$request['id']))));
						}
						else if($request['entity']=='shipment')
						{
								$result = $client->call('call', array($session, 'oopsprofile.exportshipmentdata', array(array('profileid'=>$request['id']))));
						}
						else if($request['entity']=='creditmemo')
						{
								$result = $client->call('call', array($session, 'oopsprofile.exportcreditmemodata', array(array('profileid'=>$request['id']))));
						}
						else
						{
								$result = "<error>".Mage::helper('oopsprofile')->__('Entity data not exist')."</error>";
						}
				}
				catch(Exception $e)
				{
						$result = "<error>".$e->getMessage()."</error>";
				}

				$this->getResponse()->setBody($result);

				// end session
				$client->call('endSession', array($session));
    }

    public function testAction()
    {
    		//echo 1;
    		$fileName = Mage::getBaseDir().'/samplexml.xml';
    		echo $Name = Mage::getBaseDir().'/finalxml.xml';

    		$xmlInstance = new Varien_Simplexml_Config($fileName);

    		$xml = $xmlInstance->getNode();

    		//$feed = file_get_contents($fileName);
				//$xml = new SimpleXmlElement($feed);

				//print_r($xml);

				//file_put_contents($Name,$xmlInstance->getNode()->loadString());

				$xmlArray = array();

				if($xml->hasChildren())
				{
						$subXml = $xml->children();
						foreach($subXml as $key=>$value)
						{
								echo $key;
								
								$this->_hasChildXml($value);
								
						}
				}
				else
				{
						//echo $xml;
				}
				

    		/*foreach ($xml->channel->item as $entry){
					echo $entry->title;
					echo $entry->description;
					$namespaces = $entry->getNameSpaces(true);
					foreach($namespaces as $key=>$url)
					{
							$alias = $entry->children($url);
							echo $alias->price;
					}
				}*/

								//echo '<pre>';print_r($xmlArray);

    }

    public function _hasChildXml($value)
    {
    		echo '<br/>';
    		if($value->hasChildren())
				{
						$xml = $value;
						$subXml = $xml->children();
						
						foreach($subXml as $key=>$value)
						{
								echo $key;

								
								
								$this->_hasChildXml($value);
							
								
						}
				}
				else
				{
						//echo $value;
				}
    }

    public function test2Action()
    {

    		$fileName = Mage::getBaseDir().'/samplexml.xml';
    		$Name = Mage::getBaseDir().'/finalxml.xml';

    		
    		$file = fopen($fileName, "r");
				//Output a line of the file until the end is reached
				while(!feof($file))
					{
					echo $content = fgets($file);


					$fileput = fopen($Name,"w");
					fputs($fileput,$content);
				exit;
					
					}
					
				fclose($file);
    }

    public function test3Action()
    {
    		$fileName = '<?xml version="1.0" encoding="utf-8" ?>
<xmlcontent>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">  
  <channel>  
    <title>Data feed Title</title>
    <link>{base_url}</link>
    <description>Data feed description.</description>
   	{each type="product"}
      <item>
        <title><![CDATA[{name}]]></title>
        <link><![CDATA[{url}]]></link>
        <g:price>{price}</g:price>
        <g:online_only>y</g:online_only>
        <description><![CDATA[{description,[strip_tags]}]]></description>
        <g:product_type><![CDATA[{category_path}]]></g:product_type>
        <g:google_product_category><![CDATA[]]></g:google_product_category>
        <g:image_link>{image}</g:image_link>
        <g:condition>new</g:condition>
        <g:availability>in stock</g:availability>
        <g:quantity>{qty}</g:quantity>
        <g:color><![CDATA[{color}]]></g:color>
        <g:shipping_weight>{weight, [number_format 2]} kilograms</g:shipping_weight>
        <g:manufacturer><![CDATA[{manufacturer}]]></g:manufacturer>
        <g:brand><![CDATA[{manufacturer}]]></g:brand>
        <g:mpn><![CDATA[{sku}]]></g:mpn>
        <g:gtin><![CDATA[{upc}]]></g:gtin>
      </item>
    {/each}
  </channel>
</rss>
</xmlcontent>';


$content = explode('{each type="product"}',$fileName);
$top = $content[0];
//echo $content[1];
$content1 = explode('{/each}',$content[1]);
$middle = $content1[0];
$bottom = $content1[1];
$key = 'name';
$middle = str_replace('{'.$key.'}','testing',$middle);
echo $middle;
    }

}

<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

echo 'Installing Sreedarsh Bootstrap Slider V 1.0.2: ' . get_class($this) . "\n <br /> \n";
$installer = $this;
$installer->startSetup();
$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('bsslider/bsslider')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_name` varchar(100) NOT NULL,
  `image_title` varchar(60) NOT NULL,
  `image_caption` varchar(300) NOT NULL,
  `image_link` varchar(100) NOT NULL,
  `status` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
");
$installer->endSetup();
?>
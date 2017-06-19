<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "my_custom_input_field", array("type"=>"varchar"));
//$installer->addAttribute("quote", "my_custom_input_field", array("type"=>"varchar"));
$installer->endSetup();

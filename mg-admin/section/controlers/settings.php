<?php
$dir = SITE_DIR.ltrim(URL::getCutPath(), '/').'/mg-templates';
$folderTemplate = scandir($dir);
$templates = array();
foreach($folderTemplate as $key => $foldername ){
  if (!in_array($foldername, array(".",".."))){
    if(file_exists($dir.'/'.$foldername.'/css/style.css')){
      $templates[] = $foldername;
    }
  }
}

$this->data = array(
  'options' => array(
    'sitename' => MG::getOption('sitename', true),
    'adminEmail' => MG::getOption('adminEmail', true),
    'templateName' => MG::getOption('templateName', true),
    'countСatalogProduct' => MG::getOption('countСatalogProduct', true),
    'webmoneyPurse' => MG::getOption('webmoneyPurse', true),
    'yandexPurse' => MG::getOption('yandexPurse', true),
    'orderMessage' => MG::getOption('orderMessage', true),
	'price_delivery' => MG::getOption('price_delivery', true),
	'piramida' => MG::getOption('piramida', true),
	'prize_key' => MG::getOption('prize_key', true),
	'sale' => MG::getOption('sale', true)
  ),
  'templates' => $templates
);
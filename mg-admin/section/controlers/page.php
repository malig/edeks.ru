<?php
/**
 *
 * Раздел управления страницами сайта.
 * Позволяет управлять заказами пользователей.
 *
 * @autor Авдеев Марк <mark-avdeev@mail.ru>
 */
$result = DB::query('SELECT  *  FROM `page`');

if(! empty($result)){
  while($page = DB::fetchAssoc($result)){
    $pages[] = $page;
  }
  $this->pages = $pages;
}else{
  $this->pages = array();
}



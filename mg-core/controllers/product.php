<?php
/**
 * Контроллер Product
 *
 * Класс Controllers_Product обрабатывает действия пользователей на странице товара.
 * - Находится в процессе разработки.
 */
class Controllers_Product extends BaseController{

  function __construct(){
    $model = new Models_Product;
    $product = $model->getProduct(URL::getQueryParametr('id'));
    $this->data = $product;
  }

}
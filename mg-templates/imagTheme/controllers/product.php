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
		$_REQUEST = MG::defenderXss($_REQUEST);
		$argRes = explode("/", $_REQUEST['backurl']);

		if($argRes[0] == 'search'){
			$product['backUri'] = $argRes[0].'?p='.$argRes[1].'&inputString='.$argRes[2];
		}else{
			$product['backUri'] = $argRes[0];
		}

		$this->data = $product;
	}
}
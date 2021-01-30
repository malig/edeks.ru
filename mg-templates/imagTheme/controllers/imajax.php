<?php
/**
 * Контроллер: imajax
 *
 * Класс Controllers_imajax обрабатывает AJAX запросы из публичной части.
 * - Отключает вывод шаблона;
 */
class Controllers_imajax extends BaseController{

	function __construct(){
		MG::disableTemplate();
		disableCookInSession();
		
		$cart = new Models_Cart;
		
	//Добавление товара в корзину
		if(isset($_REQUEST['inCartProductId'])){
			$cart->addToCart($_REQUEST['inCartProductId']);
			SmalCart::setCartData();
		}
	//Удаление товара из корзины
		if(isset($_REQUEST['delCartProductId'])){
			$_SESSION['cart'][$_REQUEST['delCartProductId']]--;
			$cart->refreshCart($_SESSION['cart']);
			SmalCart::setCartData();
		}

		$this->data = array (
			'isEmpty' => $cart->isEmptyCart(),
			'productPositions' => $cart->getItemsCart(),
			'totalSumm' => $cart->getTotalSumm()
		);
	}
 
}
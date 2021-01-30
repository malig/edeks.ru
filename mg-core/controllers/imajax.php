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
		if(isset($_REQUEST['inCartProductId'])){
			$cart->addToCart($_REQUEST['inCartProductId']);
			SmalCart::setCartData();
		}

		$this->data = array (
			'isEmpty' => $cart->isEmptyCart(),
			'productPositions' => $cart->getItemsCart(),
			'totalSumm' => $cart->getTotalSumm()
		);
	}
 
}
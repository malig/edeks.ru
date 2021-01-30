<?php
/**
 * Контроллер: Personal
 *
 * Класс Controllers_Personal обрабатывает действия пользователей на странице личного кабинета.
 * - Находится в процессе разработки.
 */
class Controllers_Personal extends BaseController{

	function __construct(){
		if(User::isAuth()){
			
			if(isset($_REQUEST['p'])){
			  $page = $_REQUEST['p'];
			}
			
			$model = new Models_Personal;

			$arOrder = array();
			$arOrder = $model->getPageList($page, 10, true, $_SESSION['user']->id);
			
			$pagination = $arOrder['pagination'];
			unset($arOrder['pagination']);
			
			$userInfo = User::getThis();

			$this->data = array(
				'userInfo' => USER::getUserById($userInfo ->id),
				'err' => $err,
				'arOrder' => $arOrder,
				'pagination' => $pagination
			);
		}
	}
}
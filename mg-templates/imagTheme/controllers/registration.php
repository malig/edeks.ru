<?php
/**
 * Контроллер: Registration
 *
 * Класс Controllers_Registration обрабатывает действия пользователей на странице регистрации нового пользователя.
 * - Проверяет корректность данных;
 * - Регистрирует учетную запись пользователя.
 *
 */
class Controllers_Registration extends BaseController{

	function __construct(){
		$registration = new Models_Registration;

		$data = array(
		  'dislpayForm' => true
		);
		
		$error = $registration->validDataForm();		
		
		if(strpos($_SERVER['HTTP_REFERER'], '/registration')){
			$data['error'] = $error;
		}

		if(!$error){
			$error = $registration->newUserRegistered();
			if(!$error){
				$data['isRegistered'] = true;
			}else{
				$data['error'] = $error;
			}	
		}
		
		$this->data = $data;
	}
}
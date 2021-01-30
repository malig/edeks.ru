<?php
/**
 * Контроллер: Enter
 * 
 * Класс Controllers_Enter обрабатывает действия пользователей на странице авторизации.
 * - Аутентифицирует пользовательские данные;
 * - Проверяет корректность ввода данных с формы авторизации;
 * - При успешной авторизации перенаправляет пользователя в личный кабинет.
 */
class Controllers_Enter extends BaseController{

  function __construct(){
    if(URL::getQueryParametr('logout')){
      User::logout();
    }

//Активация акк
	if(URL::getQueryParametr('act')){
		
		$result = DB::query('
			SELECT user_hash
			FROM `user`
			WHERE email = "%s"
		', URL::getQueryParametr('login'));

		if($row = DB::fetchObject($result)){
			if(crypt(URL::getQueryParametr('login'),$row->user_hash) === URL::getQueryParametr('act')){
				DB::query("UPDATE `user` SET `legal`='%s' Where `email`='%s'", '1', URL::getQueryParametr('login'));
				$this->data = array ('cool' => 'Аккаунт успешно активирован');
			}else{
				$this->data = array ('msgError' => 'Сбой активации');
			}
		}else{
			$this->data = array ('msgError' => 'Сбой активации!');
		}		
	}	
	
    //Если пользователь не авторизован.
	if(!User::isAuth() && $this->validForm()){
		if(!User::auth(URL::get('email'), URL::get('pass'))){
			$this->data = array (
			  'msgError' => 'Неверный адрес или пароль'
			);
		}else{
			if(!User::isLegal(URL::get('email'), URL::get('pass'))){
				$this->data = array (
				  'msgError' => 'Ваш аккаунт не активирован'
				);
			}else{
				$this->successfulLogon();
			}
		}
	}
  }

  /**
   * Перенаправляет пользователя на страницу в личном кабинете.
   * @return void
   */
  public function successfulLogon(){

    //если указан параметр для редиректа после успешной авторизации
    if($location = URL::getQueryParametr('location')){
      MG::redirect($location);
    }else{

      // иначе  перенаправляем в личный кабинет
      MG::redirect('/personal');
    }
  }


  /**
   * Проверяет корректность ввода данных с формы авторизации.
   * @return void
   */
  public function validForm(){
    $email = URL::getQueryParametr('email');
    $pass = URL::getQueryParametr('pass');

    if(!$email || !$pass){
      //при первом показе, не выводить ошибку
      if(strpos($_SERVER['HTTP_REFERER'], '/enter')){
        $this->msgError = "Одно из обязательных полей не заполнено!";
      }
      return false;
    }
    return true;
  }

}
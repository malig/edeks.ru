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

    //Если пользователь не авторизован.
    if(!User::isAuth() && $this->validForm()){
      if(!User::auth(URL::get('email'), URL::get('pass'))){
        $this->data = array (
          'msgError' => 'Неправильная пара email-пароль! Авторизоваться не удалось.'
        );
      }else{
        $this->successfulLogon();
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
<?php
/**
 * Контроллер: Registration
 *
 * Класс Controllers_Registration обрабатывает действия пользователей на странице регистрации нового пользователя.
 * - Проверяет корректность данных;
 * - Регистрирует учетную запись пользователя.
 */
class Controllers_Registration extends BaseController{

  function __construct(){
    $registration = new Models_Registration;

    try{
      $registration->newUserRegistered();
      $this->data = array('isRegistered' => true);
    } catch(Exception $e){
        //при первом показе, не выводить ошибку
      if(strpos($_SERVER['HTTP_REFERER'], '/registration')){
      $this->data = array('msgError' => '<span class="msgError">Ошибка!!! '.$e->getMessage().'</span><br>');
      }
    }
  }

}
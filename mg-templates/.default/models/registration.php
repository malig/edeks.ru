<?php
/**
 * Модель: Registration
 *
 * Класс Models_Registration реализует логику регистрации новых пользователей.
 * - Проверяет корректность введенных данных в форме регистрации;
 * - Регистрирует нового пользователя, заносит данные в базу сайта;
 */
class Models_Registration{

  /**
   * Проверяет корректность введенных данных в форме регистрации.
   * @return array массив данных.
   * @throws Exception Ошибки коректности данных.
   */
  public function validDataForm(){

    $data = array(
        'pass' => URL::getQueryParametr('pass'),
        'email' => URL::getQueryParametr('email'),
        'role' => 2,
        'name' => URL::getQueryParametr('name'),
        'sname' => URL::getQueryParametr('sname'),
        'address' => URL::getQueryParametr('address'),
        'phone' => URL::getQueryParametr('phone'),
    );
    if(!$data['email'] || !$data['pass']){
      throw new Exception('Одно из обязательных полей не заполнено!');
    }

    if($data['pass'] != URL::getQueryParametr('pass2')){
      throw new Exception('Пароли не совпадают!');
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $data, $args );
  }

  /**
   * Регистрирует нового пользователя, заносит данные в базу сайта.
   * @return void
   */
  public function newUserRegistered(){
    $result = false;
    if($userInfo = $this->validDataForm()){
      if(USER::add($userInfo)){
        $result =  true;
      }
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

}
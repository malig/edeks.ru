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
      $this->data = array(
        'userInfo' => User::getThis()
      );
    }
  }
}
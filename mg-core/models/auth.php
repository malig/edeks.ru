<?php
/**
 * Модель: Auth
 *
 * Класс Models_Auth реализует логику авторизации пользователей.
 * - Проверяет данные из формы авторизации;
 * - Получает параметры пользователя по его логину.
 */
class Models_Auth {


  /**
   *  Проверяет данные из формы авторизации.
   *
   *  @param string $login логин пользователя.
   *  @param string $pass пароль пользователя.
   *  @return array массив пользовательских данных.
   */
  public function validData($login, $pass) {

    // фильтрация введенного пользователем login для запроса в БД
    $login = trim(strip_tags($login));

    // фильтрация введенного пользователем пароля
    $userPass = trim(strip_tags($pass));

    // получение информации о пользователе из БП с введеным login
    $userInfo = $this->getUserInfoByLogin($login);
    $dbPass = $userInfo['pass'];

    // костылек, ограничивающий до 29 символов результат работы crypt, тк ограничение в БД - 30 символов
    $diprPass = crypt($userPass, $dbPass);
    $diprPass = substr($diprPass, 0, 29);

    // проверка соответствия пароля из БД с паролем введеным пользователем
    if($diprPass == $dbPass) {
      $_SESSION['Auth'] = true;
      $_SESSION['User'] = $login;
      $_SESSION['role'] = $userInfo['role'];
    } else {
      $_SESSION['Auth'] = false;
    }

    if(!$_SESSION['Auth']) {
      $msg = '<em><span style = "color:red">Данные введены не верно!</span></em>';
    } else {
      $msg = '<em><span style = "color:green">Вы верно ввели данные!</span></em>';
      $unVisibleForm = true;
    }

    $result = array(
      'unVisibleForm' => $unVisibleForm,
      'userName' => $login,
      'msg' => $msg,
      'login' => $login,
      'pass' => $pass,
    );
    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);
  }

  /**
   *  Получает параметры пользователя по его логину.
   *
   *  @param string $login логин пользователя.
   *  @return array массив с данным пользователя.
   */
  protected function getUserInfoByLogin($login) {
    $sql = '
      SELECT *
      FROM `user`
      WHERE login = "%s"
    ';
    $res = DB::query($sql, $login);
    $result = mysql_fetch_assoc($res);
    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);
  }
}
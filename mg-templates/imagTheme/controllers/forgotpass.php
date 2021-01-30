<?php

/**
 * Контроллер Forgotpass
 *
 * Класс Controllers_Forgotpass выполняет последовательность операций по восстановлению пароля пользователя.
 */
class Controllers_Forgotpass extends BaseController {

  function __construct() {

    if (User::isAuth()) {
      MG::redirect('/');
    }

    //шаг первый
    $form = 1;
    $fPass = new Models_Forgotpass;

    //второй шаг, производящий проверку введеного электронного адреса
    if (URL::getQueryParametr('forgotpass')) {
      $email = URL::getQueryParametr('email');

      if ($userInfo = USER::getUserInfoByEmail($email)) {
        //если введенных адрес совпадает с зарегистрированным в системе, то
        $form = 0;
        $message = 'Инструкция по восстановлению пароля была отправлена на <strong>'.$email.'</strong>';
        $hash = $fPass->getHash($email);
        //а) случайный хэш заносится в БД
        $fPass->sendHashToDB($email, $hash);
        $siteName = MG::getOption('sitename');
        $emailMessage = '
          Здравствуйте!<br>
            Вы зарегистрированы на сайте '.$siteName.' с логином '.$email.'.<br>
            Для восстановления пароля пройдите по <a href="'.SITE.'/forgotpass?sec='.$hash.'&id='.$userInfo->id.'" target="blank">ссылке</a>.<br>
            Если Вы не делали запрос на восстановление пароля, то проигнорируйте это письмо.<br>
            Отвечать на данное сообщение не нужно.';
        $emailData = array(
          'nameFrom' => $siteName,
          'emailFrom' => "noreply@".$siteName,
          'nameTo' => 'Пользователю сайта '.$siteName,
          'emailTo' => $email,
          'subject' => 'Восстановление пароля на сайте '.$siteName,
          'body' => $emailMessage,
          'html' => true
        );
        //б) на указанный электронный адрес отправляется письмо с сылкой на страницу восстановления пароля
        $fPass->sendUrlToEmail($emailData);
      } else {
        $form = 0;
        $error = 'К сожалению, такой логин не найден<br>
          Если вы уверены, что данный логин существует, пожалуйста свяжитесь, с нами.';
      }
    }
    //шаг 3.обработка перехода по ссылки. принимается id пользователя и сгенерированный хэш
    if ($_GET) {
      $userInfo = USER::getUserById(URL::getQueryParametr('id'));
      $hash = URL::getQueryParametr('sec');
      //если присланный хэш совпадает с хэшом из БД для соответствующего id
      if ($userInfo->restore==$hash) {
        $form = 2;
        //меняе в БД случейным образом хэш, делая невозможным повторный переход по ссылки
        $fPass->sendHashToDB($userInfo->email, $fPass->getHash('0'));
        $_SESSION['id'] = URL::getQueryParametr('id');
      } else {
        $form = 0;
        $error = 'Не корректная ссылка. Повторите заново запрос восстановления пароля.';
      }
    }

    //шаг 4. обрабатываем запрос на ввод нового пароля
    if (URL::getQueryParametr('chengePass')) {
      $form = 2;
      $person = new Models_Personal;
      $msg = $person->changePass(URL::getQueryParametr('newPass'), $_SESSION['id'], true);
      if ('Пароль изменен'==$msg) {
        $form = 0;
        $message = $msg.'! '.'Вы можете войти в личный кабинет по адресу <a href="'.SITE.'/enter" >'.SITE.'/enter</a>';
        //$fPass->activateUser($_SESSION['id']);
        unset($_SESSION['id']);
      } else {
        $error = $msg;
      }
    }


    $this->data = array(
      'error' => $error, //сообщение об ошибке
      'message' => $message, //информационное сообщение
      'form' => $form,          //отображение формы
      'meta_title' => 'Восстановление пароля',
      'meta_keywords' => $model->currentCategory['meta_keywords'] ? $model->currentCategory['meta_keywords'] : "забвли пароль, восстановить пароль, восстановление пароля",
      'meta_desc' => $model->currentCategory['meta_desc'] ? $model->currentCategory['meta_desc'] : "Если вы забыли пароль от личного кабинета, его модно восстановить с помощью формы восстановления паролей.",
  
    );
  }

}
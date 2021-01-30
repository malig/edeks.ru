<?php

/**
 * Модель: Feedback
 *
 * Класс Models_Cart реализует логику взаимодействия с формой оратной связи.
 * - Проверяет корректность ввода данных;
 * - Отправляет сообщения на электронные адреса пользователя и администраторов.
 */
class Models_Feedback{

  // Электронный адрес пользователя.
  private $email;

  // Сообщение пользователя.
  private $message;

  /**
   * Проверяет корректность ввода данных.
   *
   * @param array $arrayData массив с данными введенными пользователем.
   * @return bool|string $error сообщение с ошибкой в случае не корректных данных.
   */
  function isValidData($arrayData){
    $result =  false;
    if(!preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)$/', $arrayData['email'])){
      $error = 'E-mail не существует!';
    }elseif(!trim($arrayData['message'])){
      $error = 'Введите текст сообщения!';
    }

    // Если нет ощибок, то заносит информацию в поля класса.
    if($error){
      $result = $error;
    }else{
      $this->fio = trim($arrayData['fio']);
      $this->email = trim($arrayData['email']);
      $this->message = trim($arrayData['message']);
      $result =  false;
    }

    $args = func_get_args();	
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Отправляет сообщения на электронные адреса пользователя и администраторов
   * @todo реализовать отдельный клас для отправки писем
   * @return bool
   */
  function sendMail(){
    $toUser = $this->email;
    $toAdmin = MG::getOption('adminEmail');
    $subject = 'Сообщение с формы обратной связи';
    $message = $this->message;
    $headers = 'MIME-Version: 1.0'.'\r\n';
    $headers .= 'Content-type: text/html; charset=utf-8'.'\r\n';
    $headers .= 'From: site@site.ru'.'\r\n';
    $mails = explode(",", $toAdmin);

    foreach($mails as $mail){
      if(preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)$/', $mail)){
        mail($mail, $subject, $message, $headers);
      }
    }

    if(mail($toUser, $subject, $message, $headers)){
      return true;
    }else{
      return false;
    }
    return false;
  }


}
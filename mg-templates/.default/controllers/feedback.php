<?php
/**
 * Контроллер Feedback
 *
 * Класс Controllers_Feedback обрабатывает действия пользователей на странице обратной связи.
 * - Проверяет корректность ввода данных с формы обратной связи;
 * - При успешной валидации данных, отправляет сообщение админам интернет магазина, и выводит сообщение об успешной отправке.
 */
class Controllers_Feedback extends BaseController{

  function __construct(){

    $data = array(
      'dislpayForm' => true
    );

    // Если пришли данные с формы.
    if(isset($_POST['send'])){

      // Создает модель отправки сообщения.
      $feedBack = new Models_Feedback;

      // Проверяет на корректность вода
      $error = $feedBack->isValidData($_POST);
      $data['error'] = $error;

      // Если есть ошиби заносит их в переменную.
      if(!$error){
        $feedBack->sendMail();
        MG::redirect('/feedback?thanks=1');
      }
    }

    // Формирует сообщение.
    if(isset($_REQUEST['thanks'])){
      $data = array(
        'message' => 'Ваше сообщение отправленно!',
        'dislpayForm' => false
      );
    }

    $this->data = $data;
  }
}
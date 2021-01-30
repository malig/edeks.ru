<?php

/**
 * Модель: Forgotpass
 *
 * Класс Models_Forgotpass реализует логику восстановления пароля пользователей
 */
class Models_Forgotpass{

  /**
   * генерация случайного хэша
   * @param string $string - строка на основе которой готовится хэш
   * @return string случайный хэш
   */
  public function getHash($string){
    $hash = htmlspecialchars(crypt($string));
    return $hash;
  }

  /**
   * функция записи хэша в БД
   * @param string $email - электронный адрес пользователя для которого записываем хэш
   * @param string $hash - хэш
   * @return boolean резкльтат выполнения операции
   */
  public function sendHashToDB($email, $hash){

    if(DB::query('
        UPDATE `user`
        SET `restore` = "%s"
        WHERE email = "%s"
      ', $hash, $email)){
      return true;
    }

    return false;
  }

  /**
   * отправка письма с сылкой на восстановление пароля
   * @param array $emailData - массив с передаваемыми данными
   * @return boolean - результат выполнения оперции
   */
  public function sendUrlToEmail($emailData){

    if(Mailer::sendMimeMail($emailData)){
      return true;
    }

    return false;
  }

  /**
   * активация пользователя по переданному id
   *
   * @param int $id
   */
  public function activateUser($id){
    $data = array(
      'activity' => 1,
    );
    USER::update($id, $data, 1);
  }

}
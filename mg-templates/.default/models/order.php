<?php

/**
 * Модель: Order
 *
 * Класс Models_Order реализует логику взаимодействия с заказами покупателей.
 * - Проверяет корректность ввода данных в форме оформления заказа;
 * - Добавляет заказ в базу данных.
 * - Отправляет сообщения на электронные адреса пользователя и администраторов, при успешном оформлении заказа.
 * - Удаляет заказ из базы данных.
 */
class Models_Order{

  // ФИО покупателя.
  private $fio;

  // Электронный адрес покупателя.
  private $email;

  // Телефон покупателя.
  private $phone;

  // Адрес покупателя.
  private $address;

  /**
   * Проверяет корректность ввода данных в форму обратной связи.
   *
   * @param array $arrayData  массив в введнными пользователем данными.
   * @return bool|string $error сообщение с ошибкой в случае не корректных данных.
   */
  public function isValidData($arrayData){
    $result = null;

    // Корректность емайл.
    if(!preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)$/', $arrayData['email'])){
      $error = 'E-mail не существует!';
    }elseif(!trim($arrayData['address'])){
      $error = 'Введите адрес!';
    }

    // Если нет ощибок, то заносит информацию в поля класса.
    if($error)
      $result = $error;
    else{
      $this->fio = trim($arrayData['fio']);
      $this->email = trim($arrayData['email']);
      $this->phone = trim($arrayData['phone']);
      $this->address = trim($arrayData['address']);
      $this->delivery = $arrayData['delivery'];
      $this->payment = $arrayData['payment'];
      $cart = new Models_Cart();
      $this->summ = $cart->getTotalSumm();
      $result = null;
    }
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args );
  }

  /**
   * Сохраняет заказ в базу сайта
   * Добавляет в массив корзины третий параметр 'цена товара', для сохранения в заказ.
   * Это нужно для тогою чтобы в последствии вывести детальную информацию о заказе.
   * Если оставить только id то информация может оказаться не верной, так как цены меняютcя.
   * @return int $id номер заказа.
   */
  public function addOrder(){

    // текущая дата в UNIX формате
    $date = time();
    $itemPosition = new Models_Product();

    // Добавляем в массив корзины параметр 'цена товара'.
    if(!empty($_SESSION['cart'])){
      foreach($_SESSION['cart'] as $productId => $count){
        $product = $itemPosition->getProduct($productId);
        // Tсли куки не актуальны исключает попадание несуществующего продукта в заказ
        if(!empty($product)){
          $productPositions[$productId] = array('name' => $product['name'],
            'code' => $product['code'],
            'price' => $product['price'],
            'count' => $count,);
        }
      }
    }

    // Сериализует данные в строку для записи в бд.
    $orderContent = json_encode($productPositions);

    // Создает новую модель корзины, чтобы узнать сумму заказа.
    $cart = new Models_Cart();
    $summ = $cart->getTotalSumm();

    // Формируем массив параметров для SQL запроса.
    $array = array(
      'name' => $this->fio,
      'email' => $this->email,
      'phone' => $this->phone,
      'adres' => $this->address,
      'date' => $date,
      'summ' => $summ,
      'order_content' => $orderContent,
      'delivery' => $this->delivery,
      'payment' => $this->payment,
      'print' => 'N',
      'close' => 'N',
    );

    // Отдает на обработку  родительской функции buildQuery
    DB::buildQuery("INSERT INTO `order` SET", $array);

    // Заказ номер id добавлен в базу
    $id = null;
    $id = DB::insertId();

    if($id){

      if('webmoney' == $this->payment){
        $link = SITE.'/order?thanks='.$id.'&pay=webmoney&summ='.$summ;
      }

      if('yandex' == $this->payment){
        $link = SITE.'/order?thanks='.$id.'&pay=yandex&summ='.$summ;
      }

      $subj = 'Оформлена заявка №'.$id.' на сайте'.MG::getOption('sitename');
      $table .= '<br/>Имя: '.$this->fio;
      $table .= '<br/>email: '.$this->email;
      $table .= '<br/>тел: '.$this->phone;
      $table .= '<br/>адрес: '.$this->address;
      $table .= '<br/>доставка: '.$this->delivery;
      $table .= '<br/>оплата: '.$this->payment;
      $table .= '<table>';

      if(!empty($_SESSION['cart'])){
        foreach($productPositions as $productId => $product){
          $prod = $itemPosition->getProduct($productId);
          $table .= '
            <tr>
              <td>'.$prod['code'].'</td>
              <td>'.$prod['name'].'</td>
              <td>'.$product['price'].'</td>
              <td>'.$product['count'].'</td>
            </tr>';
        }
      }
      $table .= '</table>';
      $table .= '<br>К оплате:'.$summ;
      $msg = MG::getOption('orderMessage').'<br>'.$table.'
		    <br/> Оплатить заказ вы можете перейдя по ссылке: '.$link;
      $this->sendMail($this->email, $subj, $msg, $id);

      // Если заказ успешно записан, то очищает корзину.
      $cart->clearCart();
    }

    // Возвращаем номер созданого заказа.
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $id, $args );
  }


  /**
   * Удаляет заказ из базы данных.
   *
   * @param int $id id удаляемого заказа.
   * @return bool
   */
  public function deleteOrder($id){
    $result = false;
    if(DB::query('
      DELETE
      FROM `order`
      WHERE id = %d', $id)){
      $result = true;
    }

    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args );
  }


  /**
   * Функция для отправки писем в UTF-8,
   * @todo необходимо вынести эту функцию в отдельный класс
   */
  public function send_mime_mail(
    $name_from, // имя отправителя
    $email_from, // email отправителя
    $name_to, // имя получателя
    $email_to, // email получателя
    $data_charset, // кодировка переданных данных
    $send_charset, // кодировка письма
    $subject, // тема письма
    $body, // текст письма
    $html = FALSE // письмо в виде html или обычного текста
  ){
    $to = $this->mime_header_encode($name_to, $data_charset, $send_charset)
      .' <'.$email_to.'>';
    $subject = $this->mime_header_encode($subject, $data_charset, $send_charset);
    $from = $this->mime_header_encode($name_from, $data_charset, $send_charset)
      .' <'.$email_from.'>';
    if($data_charset != $send_charset){
      $body = iconv($data_charset, $send_charset, $body);
    }
    $headers = "From: $from\r\n";
    $type = ($html) ? 'html' : 'plain';
    $headers .= "Content-type: text/$type; charset=$send_charset\r\n";
    $headers .= "Mime-Version: 1.0\r\n";

    return mail($to, $subject, $body, $headers);
  }

  /**
   * Функция дляформирования корректных заголовков в письме,
   * @todo необходимо вынести эту функцию в отдельный класс.
   */
  public function mime_header_encode($str, $data_charset, $send_charset){
    if($data_charset != $send_charset){
      $str = iconv($data_charset, $send_charset, $str);
    }
    return '=?'.$send_charset.'?B?'.base64_encode($str).'?=';
  }

  public function sendMail($toUser, $subject, $message, $id){
    $sitename = MG::getOption('sitename');
    $message = str_replace('#ORDER#', $id, $message);
    $message = str_replace('#SITE#', $sitename, $message);
    $message = str_replace('№', '#', $message);
    $subject = str_replace('№', '#', $subject);
    $toAdmin = MG::getOption('adminEmail');
    $mails = explode(',', $toAdmin);

	
    foreach($mails as $mail){
      if(preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)$/', $mail)){
        $this->send_mime_mail($sitename, 'noreply@'.$sitename, '', $mail, 'utf-8', 'KOI8-R', $subject, $message, true);
      }
    }

    if($this->send_mime_mail($sitename, 'noreply@'.$sitename, '', $toUser, 'utf-8', 'KOI8-R', $subject, $message, true)){
      return true;
    }else{
      return false;
    }
  }

}

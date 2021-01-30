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
  public function isValidData(){
    $result = null;
	
    $arrayData = array(
        'email' => URL::getQueryParametr('email'),
        'fio' => URL::getQueryParametr('fio'),
        'phone' => URL::getQueryParametr('phone'),
        'nameStreet' => URL::getQueryParametr('nameStreet'),
		'dom' => URL::getQueryParametr('dom'),
		'room' => URL::getQueryParametr('room'),
		'drob' => URL::getQueryParametr('drob'),
		'node' => URL::getQueryParametr('node'),
		'streetDom' => URL::getQueryParametr('streetDom'),
		'prize_key' => URL::getQueryParametr('prize_key')
    );	
	
	// Проверка VIP номера
	if(trim($arrayData['prize_key']) == ''){
		$this->has_sale = 'N';
	}else{
		if(trim($arrayData['prize_key']) == MG::getOption('prize_key')){
			$this->has_sale = 'Y';
		}else{
			$error['prize_key'] = 'Неверный VIP номер!';
		}	
    }	
    // Корректность емайл.
    if(!preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)$/', $arrayData['email'])){
      $error['email'] = 'E-mail не существует!';
    }
	if(trim($arrayData['fio']) == '' || !preg_match("/[А-Яа-я]/iu", $arrayData['fio'])){
      $error['fio'] = 'Разрешен ввод только Кирилических символов!';
    }
	if(trim($arrayData['phone']) == '' || !preg_match("/^[0-9]{6,11}$/", $arrayData['phone'])){
      $error['phone'] = 'Разрешен ввод не менее 6 цифр';
    }	
	if(trim($arrayData['nameStreet']) == '' || !preg_match("/[А-Яа-я]/iu", $arrayData['nameStreet'])){
      $error['nameStreet'] = 'В названии улицы разрешен ввод только Кирилических символов!';
    }
	if(trim($arrayData['dom']) == '' || !preg_match("/^\d+$/", $arrayData['dom'])){
      $error['dom'] = 'В номере дома разрешен ввод только цифр!';
    }
	if(trim($arrayData['room']) == '' || !preg_match("/^\d+$/", $arrayData['room'])){
      $error['room'] = 'В номер квартиры разрешен ввод только цифр!';
    }
	if(!trim($arrayData['drob']) == ''){
		if(!preg_match("/^[А-Яа-я0-9\s]+$/iu", $arrayData['drob'])){
		  $error['drob'] = 'Разрешен ввод только Кирилических символов и цифр в дробе!';
		}else{
			$arrayData['drob'] = "/".$arrayData['drob'];
		}	
	}
	
    // Если нет ощибок, то заносит информацию в поля класса.
    if($error)
      $result = $error;
    else{
	  $this->node = trim($arrayData['node']);
      $this->fio = trim($arrayData['fio']);
      $this->email = trim($arrayData['email']);
      $this->phone = trim($arrayData['phone']);
      $this->address = trim($arrayData['streetDom']).'+'.trim($arrayData['nameStreet']).
						'+д.'.trim($arrayData['dom']).trim($arrayData['drob']).
						'+кв.'.trim($arrayData['room']);
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
          $productPositions[$productId] = array(
			'id' => $productId,
			'cat_id' => $product['cat_id'],
		    'name' => $product['name'],
            'article' => $product['article'],
            'price' => $product['price'],
            'img_url' => $product['image_url'],
            'count' => $count,
			'summm' => ($count*$product['price']));
        }
      }
    }

    // Сериализует данные в строку для записи в бд.
    $orderContent = json_encode($productPositions);

    // Создает новую модель корзины, чтобы узнать сумму заказа.
    $cart = new Models_Cart();
    $summ = $cart->getTotalSumm();

	if(isset($_SESSION['user']->id)){
		$userId = $_SESSION['user']->id;
	}else{
		$userId = '0';
	}

    // Формируем массив параметров для SQL запроса.
    $array = array(
      'name' => $this->fio,
      'email' => $this->email,
      'phone' => $this->phone,
      'adres' => $this->address,
      //'date' => $date,
      'summ' => $summ,
      'order_content' => $orderContent,
      'delivery' => $this->delivery,
      'payment' => $this->payment,
      'print' => 'N',
      'close' => 'N',
	  'user_id' => $userId,
	  'node' => $this->node,
	  'has_sale' => $this->has_sale
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
	 
/*Отправка уведомлений на почту*/
		$sitename = MG::getOption('sitename');
		$subj = 'Оформлена заявка №'.$id.' на сайте'.$sitename;
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
		$msg = MG::getOption('orderMessage').'<br>'.$table.'<br/>';
		
		$msg = str_replace('#ORDER#', $id, $msg);
		$msg = str_replace('#SITE#', $sitename, $msg);
		$msg = str_replace('№', '#', $msg);

		$mails = explode(',', MG::getOption('adminEmail'));
		foreach($mails as $mail){
			if(preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)$/', $mail)){
				Mailer::addHeaders(array("Reply-to" => $this->email));
				Mailer::sendMimeMail(array(
					'nameFrom' => $this->fio,
					'emailFrom' => $this->email,
					'nameTo' => $sitename,
					'emailTo' => $mail,
					'subject' => $subj,
					'body' => $msg,
					'html' => true
				));
			}
		}
/****/      
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

}

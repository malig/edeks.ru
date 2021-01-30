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
  	
	$result =  false;
	
	//Проверка дроби
	if(!trim(URL::getQueryParametr('drob')) == ''){
		if(!preg_match("/^[А-Яа-я0-9\s]+$/iu", URL::getQueryParametr('drob'))){
		  $error['drob'] = 'Разрешен ввод только Кирилических символов и цифр в дробе!';
		}else{
			$drob = "/".URL::getQueryParametr('drob');
		}	
	}
	
    $data = array(
        'pass' => URL::getQueryParametr('pass'),
        'email' => URL::getQueryParametr('email'),
        'role' => 2,
        'name' => URL::getQueryParametr('name'),
        'address' => URL::getQueryParametr('streetDom').'+'.
					 URL::getQueryParametr('nameStreet').'+д.'.
					 URL::getQueryParametr('dom').
					 $drob.'+кв.'.
					 URL::getQueryParametr('room'),
		'phone' => URL::getQueryParametr('phone')
    );
	

	
	// Проверка капчи.
	if(URL::getQueryParametr('capcha') != $_SESSION['capcha']){ 
		$error['errcapcha'] = "Текст с картинки введен не верно!";
	}
	
    // Проверка электронного адреса.
    if(USER::getUserInfoByEmail($data['email'])){
      $error['errmail'] = '<span class="email-in-use">Указаный email уже используется!</span>';
    }
	
	// Проверка электронного адреса.      
	if(!preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9_-]+.([A-Za-z0-9_-][A-Za-z0-9_]+)$/', $data['email'])){
		$error['errmail'] = '<span class="errorEmail">Не верно заполнено email!</span>';
	}	
	
	// Проверка на пустые поля.	
    if(!$data['pass'] || !$data['email'] || !$data['name'] || !$data['phone']){
      $error['err'] = 'Все поля обязательны к заполнению!';
    }

	// Проверка совпадения паролей.	
    if($data['pass'] != URL::getQueryParametr('pass2')){
      $error['errpass'] = 'Пароли не совпадают!';
    }
	
	// Проверка телефона.
	if(trim($data['phone']) == '' || !preg_match("/^[0-9]{6,11}$/", $data['phone'])){
		$error['errphone'] = 'Разрешен ввод не менее 6 цифр!';
    }

	//Проверка имени
	if(trim($data['name']) == '' || !preg_match("/[А-Яа-я]/iu", $data['name']) || mb_strlen($data['name'],'UTF-8')>25){
		$error['errname'] = 'Разрешен ввод только 25 кирилических символов!';
    }
	
	//Проверка улицы
	if(trim(URL::getQueryParametr('nameStreet')) == '' || !preg_match("/[А-Яа-я]/iu", URL::getQueryParametr('nameStreet'))){
      $error['nameStreet'] = 'В названии улицы разрешен ввод только Кирилических символов!';
    }
	
	//Проверка номера дома
	if(trim(URL::getQueryParametr('dom')) == '' || !preg_match("/^\d+$/", URL::getQueryParametr('dom'))){
      $error['dom'] = 'В номере дома разрешен ввод только цифр!';
    }
	
	//Проверка номера квартиры
	if(trim(URL::getQueryParametr('room')) == '' || !preg_match("/^\d+$/", URL::getQueryParametr('room'))){
      $error['room'] = 'В номер квартиры разрешен ввод только цифр!';
    }


	if($error){
		$result = $error;
    }else{	
		$this -> regParam = $data;
	}

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }
  
  /**
   * Проверка пароля
   * @return void
   */  
  public function validPass($userData, $mode){
    
    // Пароль должен быть больше 5-ти символов.
    if(strlen($userData['pass']) < 5){
      $error .= '<span class="passError">Пароль менее 5 символов</span>';
    }
      // Проверяем равенство введенных паролей.
    if(URL::getQueryParametr('pass2') != $userData['pass']){
      $error .= '<span class="wrong-pass">Введенные пароли не совпадают</span>';
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $error, $args );
  }
  

  /**
   * Регистрирует нового пользователя, заносит данные в базу сайта.
   * @return void
   */
	public function newUserRegistered(){
		$result = false;
		
		if(MG::getOption('piramida') == '1' && isset($_COOKIE['visit'])){		
			$userInfo = USER::getUserInfoByHash($_COOKIE['visit']);
			$this -> regParam['baas'] = $userInfo->id;
		}
		
		$error = USER::add($this -> regParam);
		$result = $error;

		$args = func_get_args();
		return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
	}

}
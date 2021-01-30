<?php
/**
 * Класс User - предназначен для работы с учетными записями пользователей системы.
 * Доступен из любой точки программы.
 * Реализован в виде синглтона, что исключает его дублирование.
 */
class User{

  static private $_instance = null;
  private $auth = array();

  private function __construct(){

    // Если пользователь был авторизован, то присваиваем сохраненные данные.
    if(isset($_SESSION['user'])){
      $this->auth = $_SESSION['user'];
    }
  }

  private function __clone(){

  }

  private function __wakeup(){

  }


  /**
   * Возвращет единственный экземпляр данного класса.
   * @return object - объект класса URL.
   */
  static public function getInstance(){
    if(is_null(self::$_instance)){
      self::$_instance = new self;
    }
    return self::$_instance;
  }


  /**
   * Инициализирует объект данного класса User.
   * @return void
   */
  public static function init(){
    self::getInstance();
  }


   /**
   * Возвращает авторизированнго пользователя.
   * @return void
   */
  public static function getThis(){
    return self::$_instance->auth;
  }


  /**
   * Добавляет новую учетную запись пользователя в базу сайта.
   * @param $userInfo - массив значений для вставки в БД [Поле => Значение].
   * @return bool
   */
	public static function add($userInfo){
		$salt = '$2a$10$'.substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(),mt_rand()))), 0, 22) . '$';
		$result = false;

		// Если пользователя с таким емайлом еще нет.
		if(!self::getUserInfoByEmail($userInfo['email'])){
			
			$userInfo['pass'] = crypt($userInfo['pass'],$salt);
			
			$userInfo['user_hash'] = crypt($userInfo['email'],self::generateCode(10));
			
			$userInfo['url'] = "http://".MG::getOption('sitename')."/enter?act=".crypt($userInfo['email'],$userInfo['user_hash'])."&login=".$userInfo['email'];
			
			if(DB::buildQuery('INSERT INTO user SET date_add = now(), ', $userInfo)){
				$id = DB::insertId();
				
				$siteName = MG::getOption('sitename');

				$message = "Здравствуйте!<br>".
							"Вы получили данное письмо так как зарегистрировались на сайте ".MG::getOption('sitename'). "<br>".
							"Для активации пользователя пройдите по ".
							"<a href='http://".$siteName."/enter?act=".crypt($userInfo['email'],$userInfo['user_hash']).
							"&login=".$userInfo['email']."' target='blank'>ссылке</a>.<br>";
				
				$emailData = array(
					  'nameFrom' => $siteName,
					  'emailFrom' => "noreply@".$siteName,
					  'nameTo' => 'Пользователю сайта '.$siteName,
					  'emailTo' => $userInfo['email'],
					  'subject' => 'Активация пользователя на сайте '.$siteName,
					  'body' => $message,
					  'html' => true
				);
				
				Mailer::sendMimeMail($emailData);	
			}
			
		}else{
			$result = 'Не удалось добавить пользователя, т.к. указаный email уже используется';
		}

		$args = func_get_args();
		return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);
	}

//Генерирует случайную строку
	public static function generateCode($length=6) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";

		$code = "";

		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length) {

				$code .= $chars[mt_rand(0,$clen)];  
		}
		return $code;
	} 

  /**
   * Удаляет учетную запись пользователя из базы.
   * @param $id id пользовате, чью запись следует удалить.
   * @return void
   */
  public static function delete($id){
    if(DB::query('DELETE FROM `user` WHERE id = %s', $id)){
		return true;
	}else{
		return false;
	}	
  }


  /**
  * Обновляет пользователя учетную запись пользователя.
  * @param $id - id пользователя.
  * @param $data - массив значений для вставки  в БД [Поле => Значение].
  * @return void
  */
  public static function update($id, $data){
	if(DB::query('UPDATE user SET '.DB::buildPartQuery($data).' WHERE id = %d', $id)){
		return true;
	}else{
		return false;
	}		
  }

  /**
   * Разлогинивает авторизованного пользователя.
   * @param $id - id пользователя.
   * @return void
   */
  public static function logout(){
    self::getInstance()->auth  = null;
    unset($_SESSION['user']);

    //Удаляем данные о корзине.
    //SetCookie('cart', '', time());
    MG::redirect('/enter');

  }


  /**
   * Аутентифицирует данные, с помощью сриптографического алгоритма
   * @param $email - емайл.
   * @param $pass - пароль.
   * @return bool
   */
  public static function auth($email, $pass){

    $result = DB::query('
      SELECT *
      FROM `user`
      WHERE email = "%s" 
    ', $email, $pass);

    if($row = DB::fetchObject($result)){
      if($row->pass == crypt($pass, $row->pass)){
        self::$_instance->auth = $row;
        $_SESSION['user'] = self::$_instance->auth;
        return true;
      }
    }
    return false;
  }
  
  public static function isLegal($email, $pass){

    $result = DB::query('
      SELECT *
      FROM `user`
      WHERE email = "%s" AND legal = 1
    ', $email, $pass);

    if($row = DB::fetchObject($result)){
		return true;
    }
    return false;
  }  

  /**
   * Получает все данные пользователя из БД по ID.
   * @param $id - пользователя.
   * @return void
   */
  public static function getUserById($id){
    $result = false;
    $res = DB::query('
      SELECT *
      FROM `user`
      WHERE id = "%s"
    ', $id);

    if($row = DB::fetchObject($res)){
      $result = $row;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);
  }


  /**
   * Получает все данные пользователя из БД по email.
   * @param $email - пользователя.
   * @return void
   */
  public static function getUserInfoByEmail($email){
    $result = false;
    $res = DB::query('
      SELECT *
      FROM `user`
      WHERE email = "%s"
    ',$email);

    if($row = DB::fetchObject($res)){
        $result = $row;
      }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);

  }
  
  /**
   * Получает все данные пользователя из БД по hash.
   */
  public static function getUserInfoByHash($hash){
    $result = false;
    $res = DB::query('
      SELECT *
      FROM `user`
      WHERE user_hash = "%s"
    ',$hash);

    if($row = DB::fetchObject($res)){
        $result = $row;
      }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);

  }  
  
  /**
   * Получает id пользователя БД по id заказа.
   */
  public static function getUserInfoByOrderId($orderId){
    $result = false;
    $res = DB::query('
      SELECT user_id, is_prized, summ, has_sale
      FROM `order`
      WHERE id = "%s"
    ',$orderId);

    if($row = DB::fetchObject($res)){
        $result = $row;
      }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);

  }  
  
    /**
   * Устанавливаем бонусные очки пользователю.
   */
  
  public static function setUserPrize($Prize, $idUser){
    $result = false;
	
	$array['prize'] = $Prize;

    if(DB::query('
      UPDATE user
      SET '.DB::buildPartQuery($array).'
      WHERE id = %d
    ', $idUser)){
      $result = true;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }  

  /**
   * Устанавливаем is_prized в труе.
   */  
  public static function setIsPrised($orderId){
    $result = false;
	
	$array['is_prized'] = 'Y';

    if(DB::query('
      UPDATE `order`
      SET '.DB::buildPartQuery($array).'
      WHERE id = %d
    ', $orderId)){
      $result = true;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }   

  /**
   * Проверяет, авторизован ли текущий пользователь.
   * @return void
   */
  public static function isAuth(){
    if(self::getThis()){
      return true;
    }
    return false;
  }
}
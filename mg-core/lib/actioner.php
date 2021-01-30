<?php
/**
 * Класс Actioner - предназначен для обработки административных действий,
 * таких как добавление и удалени товаров, категорий, и др. сущностей.
 *
 * @todo Описать все методы, отсортировать по алфавиту.
 * Привести к единому виду не стандартные ответы как в getPage.
 */
class Actioner{

  public $messageSucces = 'Операция выполнена успешно!';
  public $messageError = 'Операция не выполнена!';
  public $data = array();
  public function __construct(){

  }


 /**
  * Запускает один из методов данного класса.
  * @param type $action - название метода который нужно вызвать.
  */
  public function runAction($action){
    //отсекаем все что после  знака ?
    $action = preg_replace("/\?.*/s","",$action);

    $this->jsonResponse($this->$action());
    exit;
  }


/**
 * Добавляет категорию.
 * @return type
 */
  public function addCategory(){

    $id = MG::get('category')->addCategory($_POST);
    $this->messageSucces = 'Создана категория "'.$_POST['title'].'"';
    $this->messageError = 'Не удалось создать категорию!'.$id;
    return $id;
  }


/**
 * Добавляет продукт.
 * @return type
 */
  public function addProduct(){
    $model = new Models_Product;
    $id = $model->addProduct($_POST);
    $this->messageSucces = 'Создан товар "'.$_POST['name'].'"';
    $this->messageError = 'Не удалось создать категорию!'.$id;
    return $id;
  }


/**
 * Активирует плагин.
 * @return boolean
 */
  public function activatePlugin(){

    $this->messageSucces = 'Активирован плагин"'.$_POST['pluginFolder'].'"';
    $pluginFolder = $_POST['pluginFolder'];
    $res = DB::query("
      SELECT *
      FROM  `plugins`
      WHERE folderName = '%s'
      ", $pluginFolder);

    if(!DB::numRows($res)){
      $result = DB::query("
        INSERT INTO plugins
        VALUES ('%s', '1')"
        , $pluginFolder);

      MG::createActivationHook($pluginFolder);
      $this->data = PM::isHookInReg($pluginFolder);
      return true;
    }

    if($result = DB::query("
      UPDATE plugins
      SET active = '1'
      WHERE `folderName` = '%s'
      ", $pluginFolder
    )){
     MG::createActivationHook($pluginFolder);
     $this->data = PM::isHookInReg($pluginFolder);
      return true;
    }
    return false;
  }


/**
 * Деактивирует плагин.
 * @return boolean
 */
   public function deactivatePlugin(){
    $this->messageSucces = 'Деакивирован плагин"'.$_POST['pluginFolder'].'"';
    $pluginFolder = $_POST['pluginFolder'];
    $res = DB::query("
      SELECT *
      FROM  `plugins`
      WHERE folderName = '%s'
      ", $pluginFolder);

    if(DB::numRows($res)){
      DB::query("
        UPDATE plugins
        SET active = '0'
        WHERE `folderName` = '%s'
      ", $pluginFolder
      );

      MG::createDeactivationHook($pluginFolder);
      return true;
    }

    return false;
  }


/**
 * Удаляет картинку товара.
 * @todo также удалять картинку из папки
 * @return type
 */
  public function delImage(){
    $model = new Models_Product;
    $id = $_POST['id'];
    unset($_POST['url']);
    unset($_POST['id']);
    $array['image_url'] = '';
    return $model->updateProduct($array, $id);
  }


  /**
   * Удаляет инсталятор.
   * @return void
   */
  public function delInstal(){
    $instellDir = SITE_DIR.'install/';
    $this->removeDir($instellDir);
    MG::redirect('');
  }


  /**
   * Удаляет папку со всем ее содержимым.
   * @param string $path путь к удаляемой папке.
   * @return void
   */
  public function removeDir($path){
    if(file_exists($path) && is_dir($path)){
      $dirHandle = opendir($path);

      while(false !== ($file = readdir($dirHandle))){

        if($file != '.' && $file != '..'){// Исключаем папки с назварием '.' и '..'
          $tmpPath = $path.'/'.$file;
          chmod($tmpPath, 0777);

          if(is_dir($tmpPath)){  // Если папка.
            $this->removeDir($tmpPath);
          }else{

            if(file_exists($tmpPath)){
              // Удаляем файл.
              unlink($tmpPath);
            }
          }
        }
      }
      closedir($dirHandle);

      // Удаляем текущую папку.
      if(file_exists($path)){
        rmdir($path);
      }
    }
  }

  /**
   * Добавляет картинку для использования в nicEditor.
   * @return boolean
   */
  public function nicUpload(){
    new nicUpload();
  }
  /**
   * Добавляет картинку товара.
   * @return boolean
   */
  public function addImage(){

    $path = 'uploads/';

    $validFormats = array('jpg', 'png', 'gif', 'bmp');
    if(isset($_POST) && 'POST' == $_SERVER['REQUEST_METHOD']){

      if(!empty($_FILES['photoimg'])){
        $file_array = $_FILES['photoimg'];
      }else{
        $file_array = $_FILES['edit_photoimg'];
      }

      $name = $file_array['name'];
      $size = $file_array['size'];

      if(strlen($name)){
        list($txt, $ext) = explode('.', $name);
        if(in_array($ext, $validFormats)){
          if($size < (1024 * 1024)){
            $actualImageName = str_replace(' ', '_', $txt).'.'.$ext;
            $tmp = $file_array['tmp_name'];
            if(move_uploaded_file($tmp, $path.$actualImageName)){

                $this->data = array('img' => $actualImageName );
                $this->messageSucces = 'Изображение загружено';
              return true;

            }else{
               $this->messageError =  'Не удалось загрузить изображение';
               return false;
            }
          }else{
             $this->messageError = 'Размер изображения больше 1 МБ';
             return false;
          }
        }else{
          $this->messageError =  'Формат изображения не поддерживается';
          return false;
        }
      }else{
         $this->messageError =  "Пожалуйста выберите файл";
          return false;
      }
    }
    return false;
  }


/**
 * Удаляет категорию.
 * @return type
 */
  public function deleteCategory(){
    $this->messageSucces = 'Удалена категория "'.$_POST['title'].'"';
    $this->messageError = 'Не удалось удалить категорию!';
    return MG::get('category')->delCategory($_POST['id']);
  }


/**
 * Удаляет страницу.
 * @return boolean
 */
  public function deletePage(){
    $this->messageSucces = 'Удалена страница  №'.$_POST['id'];
    $this->messageError = 'Не удалось удалить страницу!';
    if(DB::query('DELETE FROM `page` WHERE `id`= '.$_POST['id'])){
      return true;
    }
    return false;
  }


/**
 * Удаляет товар.
 * @return type
 */
  public function deleteProduct(){
    $this->messageSucces = 'Удален товар "'.$_POST['title']."'";
    $this->messageError = 'Не удалось удалить товар!';
    $model = new Models_Product;
    return $model->deleteProduct($_POST['id']);
  }


/**
 * Удаляет заказ
 * @return type
 */
  public function deleteOrder(){
    $this->messageSucces = 'Удален заказ №'.$_POST['id'];
    $this->messageError = 'Не удалось удалить заказ!';
    $model = new Models_Order;
    return $model->deleteOrder($_POST['id']);
  }

/**
 * Удаляет пользователя
 * @return type
 */
  public function deleteUser(){
  	$id = URL::getQueryParametr('id');
    $this->messageSucces = 'Удален пользователь ID '.$id;
    $this->messageError = 'Не удалось удалить пользователя!';
	
	if(USER::delete($id)){
		return true;
	}
		return false;
  }

/**
 * Удаляет категорию.
 * @return boolean
 */
  public function editCategory(){
    $this->messageSucces = 'Изменена категория "'.$_POST['title'].'"';
    $this->messageError = 'Не удалось изменить категорию!';

    $id = $_POST['id'];
    unset($_POST['id']);
    // Если назначаемая категория, является тойже.
    if($_POST['parent'] == $id){
       $this->messageError = 'Нельзя назначить выбраную категорию родительской!';
       return false;
    }

    $childsCaterory = MG::get('category')->getCategoryList($id);
    // Если есть вложенные, и одна из них назначена родительской.
    if(!empty($childsCaterory)){
      foreach($childsCaterory as $cateroryId){
        if($_POST['parent']==$cateroryId){
          $this->messageError = 'Нельзя назначить выбраную категорию родительской!';
          return false;
        }
      }
    }

    if($_POST['parent'] == $id){
       $this->messageError = 'Нельзя назначить выбраную категорию родительской!';
       return false;
    }

    return MG::get('category')->editCategory($id, $_POST);
  }


  /**
   * Изменяет параметры товара.
   * @return type
   */
  public function editProduct(){
    $this->messageSucces = 'Товар изменен';
    $this->messageError = 'Не удалось изменить параметры товара!';
    $model = new Models_Product;
    $id = $_POST['id'];
    unset($_POST['url']);
    unset($_POST['id']);
    return $model->updateProduct($_POST, $id);
  }


/**
 * Изменяет настройки.
 * @return boolean
 */
  public function editSettings(){
    $this->messageSucces = 'Настройки сохранены';
    unset($_POST['url']);
    foreach($_POST as $option => $value){
      if(!DB::query("UPDATE `setting` SET `value`='%s' Where `option`='%s'", $value, $option)){
        return false;
      }
    }
    return true;
  }


/**
 * Получает параметры статической страницы.
 */
  public function getPage(){
    $result = DB::query('SELECT * FROM `page` WHERE `id` = '.$_POST['id']);

    if($page = DB::fetchObject($result)){
      $response = array(
          'title' => $page->title,
          'url' => str_replace('.html', '', $page->url),
          'html_content' => $page->html_content,
          'status' => 'succes'
      );
    }else{
      $response = array('msg' => 'Не удалось считать данные страницы',
          'status' => 'error');
    }

    echo json_encode($response);
    exit;
  }


/**
 * Получает параметры продукта.
 */
  public function getProduct(){
    $result = DB::query('SELECT * FROM `product` WHERE `id` = '.$_POST['id']);

    if($response = DB::fetchAssoc($result)){
      $response['status'] = 'succes';
    }else{
      $response = array('msg' => 'Не удалось считать данные товара',
          'status' => 'error');
    }

    echo json_encode($response);
    exit;
  }

/**
 * Изменяет данные заказа.
 * @return boolean
 */
  public function saveOrders(){
    $this->messageSucces = 'Заказ изменен';
    $this->messageError = 'Не удалось изменить параметры заказа!';
    if('1' == $_POST['print']){
      $_POST['print'] = 'Y';
    }
    if('0' == $_POST['print']){
      $_POST['print'] = 'N';
    }
    if('1' == $_POST['close']){
      $_POST['close'] = 'Y';
	  
	//Списание баллов
		$orderInfo = USER::getUserInfoByOrderId($_POST['order_id']);
		
		$priceDelivery = MG::getOption('price_delivery');
		$sale = MG::getOption('sale');
		
		if($orderInfo->has_sale == 'Y'){
			$priceDelivery = $priceDelivery - $sale;
		}
		
		$finishSumm = $priceDelivery + $orderInfo->summ;
		
	//Если заказчик зарегистрирован, получаем инфу о нём
		if($orderInfo ->user_id !== "0"){
			
			$userInfo = USER::getUserById($orderInfo ->user_id);
			
			if($userInfo->prize <= $finishSumm){
				USER::setUserPrize(0, $userInfo->id);
			}else{
				$diff = $userInfo->prize - $finishSumm;
				USER::setUserPrize($diff, $userInfo->id);
			}	
		}	
	  
	  //Если акция пирамида еще работает, расчитать и начислить бонусы для всех участников
		if(MG::getOption('piramida') == '1'){
			$piramida = new Models_Piramida;
			if($userId = $piramida -> getFirstParent($_POST['order_id'])){
				$piramida -> toChargePrize($userId);
			}
		}
    }
    if('0' == $_POST['close']){
      $_POST['close'] = 'N';
    }

    unset($_POST['url']);

    if(DB::query("
      UPDATE `order` SET `print`='%s', `close`='%s', `node`='%s'
      Where `id`='%s'", $_POST['print'], $_POST['close'], $_POST['node'], $_POST['order_id'])){
      return true;
    }
    return false;
  }


/**
 * Изменяет данные пользователя.
 * @return boolean
 */
	public function saveUser(){
		$this->messageSucces = 'Пользователь изменен';
		$this->messageError = 'Не удалось изменить параметры пользователя!';

		$data['address'] = URL::getQueryParametr('address');
		$data['baas'] = URL::getQueryParametr('baas');
		$data['email'] = URL::getQueryParametr('email');
		$data['legal'] = URL::getQueryParametr('legal');
		$data['name'] = URL::getQueryParametr('name');
		$data['phone'] = URL::getQueryParametr('phone');
		$data['prize'] = URL::getQueryParametr('prize');
		$data['role'] = URL::getQueryParametr('role');
		$id = URL::getQueryParametr('id'); 

		if(USER::update($id, $data)){
			return true;
		}
		return false;
	}

/**
 * Изменяет данные страницы.
 */
  public function savePage(){

    if('create_page' == $_POST['status']){

      $sql = "
        INSERT INTO `page` (`id` ,`title` ,`url` ,`html_content`)
        VALUES (
        '', ".DB::quote($_POST['title']).", ".DB::quote($_POST['filename']).", ".DB::quote($_POST['content_page'])."
        );
      ";


      $result = DB::query($sql);
      $id = DB::insertId();
      $response = array(
        'data' => array(
            'id' => $id,
        ),
        'msg' => 'Страница '.$_POST['filename'].' создана',
        'status' => 'succes',
      );

      echo json_encode($response);
      exit;
    }

    if('update_page' == $_POST['status']){
      $sql = "
        UPDATE `page` SET
          `title` = ".DB::quote($_POST['title']).",
          `url` = ".DB::quote($_POST['filename']).",
          `html_content` = ".DB::quote($_POST['content_page'])."
        WHERE `id` =
      ".$_POST['id'];

      $result = DB::query($sql);
      $response = array('msg' => 'Страница '.$_POST['filename'].' измененна',
          'status' => 'succes',);

      echo json_encode($response);
      exit;
    }
  }

/**
 * Возвращает ответ в формате JSON.
 * @param type $flag - если отработаный метод что-то вернул, то ответ считается успешным ждущей его фунции.
 * @return boolean
 */
  public function jsonResponse($flag){
    if($flag===null){return false;}
    if($flag){
      $this->jsonResponseSucces($this->messageSucces);
    }else{
      $this->jsonResponseError($this->messageError);
    }
  }


/**
 * Возвращает положительный ответ с сервера.
 * @param type $message
 */
  public function jsonResponseSucces($message){
    $result = array(
        'data' => $this->data,
        'msg' => $message,
        'status' => 'succes');
    echo json_encode($result);
  }

/**
 * Возвращает отрицательный ответ с сервера.
 * @param type $message
 */
  public function jsonResponseError($message){
    $result = array(
        'data' => $this->data,
        'msg' => $message,
        'status' => 'error');
    echo json_encode($result);
  }


  /**
   * Проверяет актуальность текущей версии системы.
   * @return void возвращает в AJAX сообщение о результате операции.
   */
  public function checkUpdata(){
    $msg = Updata::checkUpdata();

    if('У Вас последняя версия' == $msg){
      $status = 'alert';
    }else{
      $status = 'succes';
    }
    $response = array(
      'msg' => $msg,
      'status' => $status,
    );

    echo json_encode($response);
    exit;
  }

  /**
   * Обновленяет верcию CMS.
   *
   * @return void возвращает в AJAX сообщение о результате операции.
   */
  public function updata(){
    $version = $_POST['version'];

    if(Updata::updataSystem($version)){
      $msg = 'Версия обновлена';
      $status = 'succes';
    }else{
       $msg = 'Ошибка обновления';
       $status = 'error';
    }

    $response = array(
      'msg' => $msg,
      'status' => $status,
    );

    echo json_encode($response);
  }


  /**
   * Отключает публичную часть сайта. Обычно требуется для внесения изменений администратором.
   * @return bool
   */
  public function downTime(){
    $downtime = MG::getOption('downtime');

    if('Y' == $downtime){
      $activ = 'N';
    }else{
      $activ = 'Y';
    }

    $res = DB::query('
      UPDATE setting
      SET `value` = "'.$activ.'"
      WHERE `option` = "downtime"
    ');

    if ($res){
      return true;
    };
  }

}
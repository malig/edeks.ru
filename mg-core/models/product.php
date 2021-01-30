<?php

/**
 * Модель: Product
 *
 * Класс Models_Product реализует логику взаимодействия с товарами магазина.
 * - Добавляет товар в базу данных;
 * - Изменяет данные о товаре;
 * - Удаляет товар из базы данных;
 * - Получает информацию о запрашиваемом товаре;
 * - Получает продукт по его URL;
 * - Получает цену запрашиваемого товара по его id.
 */

class Models_Product{


  /**
   * Добавляет товар в базу данных.
   *
   * @param array $array массив с данными о товаре.
   * @return bool|int в случае успеха возвращает id добавленного товара.
   */
  public function addProduct($array){
    $result = false;
    $array['url'] = htmlspecialchars(MG::translitIt($array['name']));

    if(strlen($array['url']) > 60){
      $array['url'] = substr($array['url'], 0, 60);
    }

    // Исключает дублирование.
    $dublicatUrl = false;
    $tempArray = $this->getProductByUrl($array['url']);
    if(!empty($tempArray)){
      $dublicatUrl = true;
    }

    if(DB::buildQuery('INSERT INTO product SET ', $array)){
      $id = DB::insertId();
      // Если url дублируется, то дописываем к нему id продукта.
      if($dublicatUrl){
        $this->updateProduct(array('url'=>$array['url'].'_'.$id), $id);
      }
      $result = $id;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }


  /**
   * Изменяет данные о товаре
   *
   * @param array $array массив с данными о товаре.
   * @param int $id  id изменяемого товара.
   * @return bool
   */
  public function updateProduct($array, $id){
    $result = false;

    if(DB::query('
      UPDATE product
      SET '.DB::buildPartQuery($array).'
      WHERE id = %d
    ', $id)){
      $result = true;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }


  /**
   * Удаляет товар из базы данных.
   *
   * @param int $id  id удаляемого товара
   * @return bool
   */
  public function deleteProduct($id){
    $result = false;

    if(DB::query('
      DELETE
      FROM product
      WHERE id = %d
    ', $id)){
      $result = true;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }


  /**
   * Получает информацию о запрашиваемом товаре.
   *
   * @param int $id id запрашиваемого товара.
   * @return array массив с данными о товаре.
   */
  public function getProduct($id){
    $result = array();
    $res = DB::query('
      SELECT *
      FROM `product`
      WHERE id = %d
    ', $id);

    if(!empty($res)){
      if($product = DB::fetchAssoc($res)){
        $result = $product;
      }
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }


  /**
   * Получает продукт по его URL.
   *
   * @param string $url запрашиваемого товара.
   * @return array массив с данными о товаре.
   *
   */
  public function getProductByUrl($url){
    $result = array();
    $res = DB::query('
      SELECT *
      FROM `product`
      WHERE url = "%s"
    ', $url);

    if(!empty($res)){
      if($product = DB::fetchArray($res)){
        $result = $product;
      }
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }


  /**
   * Получает цену запрашиваемого товара по его id.
   *
   * @param int $id  id изменяемого товара.
   * @return bool|float $error в случии ошибочного запроса .
   */
  public function getProductPrice($id){
    $result =  false;
    $res = DB::query('
      SELECT price
      FROM product
      WHERE id = %d
    ', $id);

    if($row = DB::fetchObject($res)){
      $result =  $row->price;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }
}
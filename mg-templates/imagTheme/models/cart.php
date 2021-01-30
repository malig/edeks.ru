<?php
/**
 * Модель: Cart
 *
 * Класс Models_Cart реализует логику взаимодействия с корзиной товаров.
 * - Добавляет товар в корзину;
 * - Получает список id продуктов из корзины;
 * - Расчитывает суммарную стоимость всех товаров в корзине;
 * - Очищает содержимое корзины.
 * - Обновляет содержимое корзины.
 * - Проверяет корзину на заполненность.
 * - Получает данные о всех продуктах в корзине.
 */
class Models_Cart{


  /**
   * Добавляет товар в корзину.
   *
   * @param int $id id товара.
   * @param int $count количество.
   * @return bool
   */
  public function addToCart($id, $count = 1){
    $_SESSION['cart'][$id] = $_SESSION['cart'][$id] + $count;

    $args = func_get_args();
    $result = true;
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }


  /**
   * Возвращает список id продуктов из корзины.
   *
   * @return array список id.
   */
  protected function getListItemId(){
    $args = func_get_args();
    $result = null;

    if(!empty($_SESSION['cart'])){
      $result = array_keys($_SESSION['cart']);
    }

    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args );
  }


  /**
   * Возвращает суммарную стоимость всех товаров в корзине
   *
   * @return float
   */
  public function getTotalSumm(){

    // Получает списоr id.
    $arrayProductId = $this->getListItemId();

    // Создает модель для работы с продуктами.
    $itemPosition = new Models_Product();

    // Получает информацию о каждом продукте.
    if(!empty($arrayProductId)){
      foreach($arrayProductId as $id){
        $productPositions[] = $itemPosition->getProduct($id);
      }
    }

    // Расчитывает сумму.
    if(!empty($productPositions)){
      foreach($productPositions as $product){
        $totalSumm += $_SESSION['cart'][$product['id']] * $product['price'];
      }
    }

    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $totalSumm, $args );
  }


  /**
   * Очищает содержимое корзины.
   *
   * @return void
   */
  public function clearCart(){
    unset($_SESSION['cart']);
    MG::createHook(__CLASS__."_".__FUNCTION__);
  }


  /**
   * Обновляет содержимое корзины.
   * Перебирает полученный массив с товарами, добавляея данные в сессию.
   *
   * @param array $arrayProductId  массив id продуктамв.
   * @return void
   */
  public function refreshCart($arrayProductId){

    // Получает ассоциативный массив id=>count .
    foreach($arrayProductId as $ItemId => $newCount){
      if(0 >= $newCount){

        // Если количесво меньше нуля, то удаляет запись.
        unset($_SESSION['cart'][$ItemId]);
      }else{

        // Иначе присваивает новое количество.
        $_SESSION['cart'][$ItemId] = $newCount;
      }
    }

    MG::createHook(__CLASS__."_".__FUNCTION__);
  }


  /**
   * Проверяет корзину на заполненность.
   *
   * @return bool
   */
  public function isEmptyCart(){
    $result = false;

    if($_SESSION['cart']){
      $result = true;
    }
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args );
  }

  /**
   * Возвращает данные о всех продуктах в корзине.
   *
   * @return array
   */
  public function getItemsCart(){
    $arrayProductId = array();
    $productPositions = array();

    // Получает список id.
    $arrayProductId = $this->getListItemId();

    // Создает модель для работы с продуктами.
    $itemPosition = new Models_Product();

    if(!empty($arrayProductId)){

      foreach($arrayProductId as $id){

        // Заполняет массив информацией о каждом продукте по id из куков.
        // Если куки не актуальны, пропускает товар.
        $product = $itemPosition->getProduct($id);
        if(!empty($product)){
          $productPositions[] = $product;
        }
      }
    }

    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $productPositions, $args );
  }

}
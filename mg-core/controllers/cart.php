<?php
/**
 * Контроллер: Cart
 *
 * Класс Controllers_Cart обрабатывает действия пользователей в корзине интернет магазина.
 * - Пересчитывает суммарную стоимость товаров в корзине;
 * - Очищает корзину;
 * - Подготавливает массив данных $data для вывода в шаблоне.
 */
class Controllers_Cart extends BaseController{

  /**
   * Определяет поведение при изменении и удаление данных в корзине,
   * а так же выводит список позиций к заказу
   *
   * @return void
   */
  public function __construct(){
    $model = new Models_Cart;

    // если пользователь изменил данные в корзине
    if($_REQUEST['refresh']){
      $listItemId = $_REQUEST;

      // пробегаем по массиву, находим пометки на удаление и на изменение количества
      foreach($listItemId as $ItemId => $newCount){
        $id = '';

        if('item_' == substr($ItemId, 0, 5)){
          $id = substr($ItemId, 5);
          $count = $newCount;
        }elseif('del_' == substr($ItemId, 0, 4)){
          $id = substr($ItemId, 4);
          $count = 0;
        }

        if($id){
          $arrayProductId[$id] = (int) $count;
        }
      }

      // передаем в модель данные для обновления корзины
      $model->refreshCart($arrayProductId);

      // пересчитываем маленькую корзину
      SmalCart::setCartData();
      header('Location: '.SITE.'/cart');
      exit;
    }

    // если пользователь изменил данные в корзине
    if($_REQUEST['clear']){

      // передаем в модель данные для обновления корзины
      $model->clearCart();

      // пересчитываем маленькую корзину
      SmalCart::setCartData();
      header('Location: '.SITE.'/cart');
      exit;
    }

    // формируем стандартный массив для представления
    $this->data = array (
      'isEmpty' => $model->isEmptyCart(),
      'productPositions' => $model->getItemsCart(),
      'totalSumm' => $model->getTotalSumm()
    );

  }

}
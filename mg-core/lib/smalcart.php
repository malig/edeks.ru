<?php

/**
 * Класс SmalCart - моделирует данные для маленькой корзины.
 *  - Сохраняет содержимое корзины в куках;
 *  - Предоставляет массив с количеством товаров и их общей стоимостью.
 */
class SmalCart {
  
  /**
   * Записывает в cookie текущее состояние 
   * корзины в сериализованном виде.
   * @return void
   */
  public static function setCartData() {

    // Сериализует  данные корзины из сессии в строку.
    $cartContent = json_encode($_SESSION['cart']);

    // Записывает сериализованную строку в куки, хранит 1 год.
    SetCookie('cart', $cartContent, time() + 3600 * 24 * 365);
    MG::createHook(__CLASS__."_".__FUNCTION__, $cartContent);
  }


  /**
   * Получает данные из куков назад в сессию.
   * @return bool
   */
  public static function getCokieCart() {
	$result = false;

	// Если куки существуют.
	if (isset($_COOKIE)) {

	  // Десериализует строку в массив.
		if (!$_SESSION['noCookInSesson']) {
			$_SESSION['cart'] = json_decode($_COOKIE['cart'],true);
		}
	  $result = true;
	}

	$args = func_get_args();
	return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }


  /**
   * Вычисляет общую стоимость содержимого, а также количество.
   * @return array массив с данными о количестве и цене.
   */
  public static function getCartData() {

    // Количество вещей в корзине.
    $res['cart_count'] = 0;

    // Общая стоимость.
    $res['cart_price'] = 0;

    // Если удалось получить данные из куков и они успешно десериализованы в $_SESSION['cart'].
    if (self::getCokieCart() && $_SESSION['cart']) {

      // Пробегаем по содержимому, вычилсяя сумму и количество.
      foreach ($_SESSION['cart'] as $id => $count) {
        $result = DB::query('SELECT p.price FROM product p WHERE id=%d', $id);

        if ($row = DB::fetchAssoc($result)) {
          $totalPrice += $row['price'] * $count;
          $totalCount += $count;
        }
      }
      $res['cart_count'] = $totalCount;
      $res['cart_price'] = $totalPrice;
    }

    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $res, $args);
  }
}
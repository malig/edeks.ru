<?php
/**
 * Контроллер: Catalog
 *
 * Класс Controllers_Catalog обрабатывает действия пользователей в каталоге интернет магазина.
 * - Формирует список товаров для конкретной страницы;
 * - Добавляет товар в корзину.
 */
class Controllers_Catalog extends BaseController{

  function __construct(){

    // если нажата кнопка купить
    $_REQUEST['category_id'] = URL::getQueryParametr('category_id');
    if($_REQUEST['inCartProductId']){
      $cart = new Models_Cart;
      $cart->addToCart($_REQUEST['inCartProductId']);
      SmalCart::setCartData();
      MG::redirect('/cart');
    }

    // показать первую страницу выбранного раздела
    $page = 1;

    // сколько выводить на странице объектов
    $step = MG::getOption('countСatalogProduct');

    if(!is_numeric($step) || 1 > $step){
      $step = 1;
    }

    //запрашиваемая страница
    if(isset($_REQUEST['p'])){
      $page = $_REQUEST['p'];
    }

    // модель каталога
    $model = new Models_Catalog;

    //получаем список вложенных категорий, для вывода всех продуктов, на страницах текущей категории
    $model->categoryId = MG::get('category')->getCategoryList($_REQUEST['category_id']);

    // в конец списка, добавляем корневую текущую категорию
    $model->categoryId[] = $_REQUEST['category_id'];

    // передаем номер требуемой страницы, и количество выводимых объектов
    $items = $model->getPageList($page, $step);
    $pager = $items['pagination'];
    unset($items['pagination']);

    $this->data = array(
      'items' => $items,
      'titeCategory' => $model->currentCategory['title'],
      'pager' => $pager
    );
  }

}
<?php
/**
 * Класс Breadcrumbs - предназначен для управления и вывода  цепочки навигации по каталогу.
 */
class Breadcrumbs{
/**
 * Метод выдает строку "хлебных крошек".
 *
 * Происхрдит преобазование частей url адреса в ссылки.
 * @return string
 */
  public static function getBreadcrumbs(){
    $sections = URL::getSections();
    array_splice($sections, 0, 1);
    $breadcrumbs = '<a href="'.SITE.'/">Главная</a>';

    foreach($sections as $section){
      // для начала секцию url ищем в меню
      $res = self::_checkMenu($section);
      if(!$res){

        // затем в категориях
        $cat = 'title';
        $result = self::_checkURLname($cat, 'category', $section);
        $url = $section;
        if(!$result[0][$cat]){
          //и на конец в каталоге
          $cat = 'name';
          $result = self::_checkURLname('*', 'product', $section);
          $url = $sections[1].'/'.$sections[2];

          $categoryRes = self::_checkURLname('url', 'category', $result[0]['cat_id'], 'id');
          $url = $categoryRes[0]['url'].'/'.$result[0]['url'];
        }

        $res = $result[0][$cat];

      }

      if(!$res){
        switch($section){
          case 'cart':
            $res = 'Корзина';
            break;
          case 'order':
            $res = 'Оформление заказа';
            break;
          case 'enter':
            $res = 'Авторизация';
            break;
        }
        $url = $section;
      }

      $breadcrumbs .= '/<a href="'.SITE.'/'.$url.'">'.$res.'</a>';
    }

    return $breadcrumbs;
  }
  /**
   * Метод работает с БД, получая значение по передаваемым параметрам.
   *
   * @param string $col что.
   * @param string $table от куда.
   * @param string $name условие соответствие.
   * @return array массив с результатом.
   */
  private static function _checkURLname($col, $table, $name, $whereCol = 'url'){
    $sql = '
      SELECT %s
      FROM %s
      WHERE %s="%s "
    ';
    $result = DB::query($sql,$col,$table, $whereCol, $name);

    while($row = DB::fetchArray($result)){
      $categories[] = $row;
    }
    if($result){
      return $categories;
    }
  }
  /**
   * Проверка существования переданной секции в пунктах меню.
   * @param string $section.
   * @return boolean string $sec найденный пункт меню или false в случае неудачи.
   */
  private static function _checkMenu($section){

    if(!$section){
      return false;
    }

    $menu = Menu::getArrayMenu();

    foreach($menu as $sec => $url){

      if($section == $url){
        return $sec;
      }
    }

    return false;
  }

}
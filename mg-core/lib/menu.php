<?php

/**
 * Класс Menu - задает пункты меню сайта.
 */
class Menu{

  private function __construct(){

  }

  /**
   * Возвращает меню в HTML виде.
   * @return type
   */
  public static function getMenu(){
    $MenuItem = self::getArrayMenu();
    $print = '<ul>';

    foreach($MenuItem as $name => $item){

      if('Вход' == $name && '' != $_SESSION['User']){
        $print .= '<li><a href='.SITE.'"/enter">'.$_SESSION['User'].'</a><a class="logOut" href="enter?out=1"><span style="font-size:10px">[ выйти ]</span></a></li>';
      }else{
        $print .= '<li><a href="'.SITE.'/'.$item.'">'.$name.'</a></li>';
      }
    }
    $print .= '</ul>';
    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $print, $args);
  }

  /**
   * Возвращает массив пунктов меню.
   * @return type
   */
  public static function getArrayMenu(){
    $MenuItem = array(
        'Главная' => '',
        'Каталог' => 'catalog',
        'Обратная связь' => 'feedback',
        'Доставка' => 'dostavka'
    );
    return $MenuItem;
  }

}
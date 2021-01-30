<?php

/**
 * Модель: Catalog
 *
 * Класс Models_Catalog реализует логику работы с каталогом.
 * - Проверяет данные из формы авторизации;
 * - Получает параметры пользователя по его логину.
 */
class Models_Catalog{

  // Массив с категориями продуктов.
  public $categoryId = array();
  // Массив текущей категории
  public $currentCategory = array();
  // Фильтр пользователя.
  public $userFilter = array();

  /**
   * Формирует массив ссылок на категории продуктов, учитывает выбранную страницу списка.
   * В полученный массив добавляет информацию для постраничной навигации.
   *
   * @param int $page  номер текущей страницы.
   * @param int $step  количество выводимых записей.
   * @param int $mgadmin флаг для построения ссылок навигации в админке и на сайте.
   * @return array
   */
  function getPageList($page = 1, $step = 5, $mgadmin = false){

     $temparray = $this->getList($page, $step);
     $page = $temparray['page'];
     $count = $temparray['count'];
     $catalogItems = $temparray['catalogItems'];

    /**
     * Делает постраничную навигацию.
     * Устанавливаем активную страницу.
     */
    $activPage = $page;

    // Получаем урл секции, если его нет то заменяет на "catalog".
    $urlPage = $this->currentCategory['url'];

    if(1 < $count){
    // перебирает все страницы и формирует ссылки на них.
      for($page = 0; $page < $count; $page++){
        ($activPage == $page) ? $class = 'activ' : $class = '';
        if(!$mgadmin){
          $pages .= '<a class = "'.$class.'" href = "'.SITE.'/'.$urlPage.'?p='.($page + 1).'">'.($page + 1).'</a>';
        } else{
          $pages .= '<a rel="pagination" page = "'.($page + 1).'" class = "'.$class.'" href = "#">'.($page + 1).'</a>';
        }
      }
      $pages = '<div class = "pagination">Страница '.($activPage + 1).' из '.($count).' '.$pages.'</div>';
    }

    // Дописывает  к возвращаемому массиву информацию о пагинации.
    $catalogItems['pagination'] = $pages;
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $catalogItems, $args);
  }

  /**
   * Получает ссылку и название текущей категории.
   *
   * @access protected
   * @return bool
   */
  protected function getCurrentCategory(){
    $result = false;

    $sql = '
      SELECT url, title
      FROM category
      WHERE id = %d
    ';

    if(end($this->categoryId)){
      $result = DB::query($sql, end($this->categoryId));

      if($this->currentCategory = DB::fetchAssoc($result)){
        $result = true;
      }
    }else{
      $this->currentCategory['url'] = 'catalog';
      $this->currentCategory['title'] = 'Каталог';
      $result = true;
    }

    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }

  /**
   * Формирует массив ссылок на категории продуктов.
   *
   * @param int $page номер текущей страницы.
   * @param int $step количество выводимых записей.
   * @return array
   */
    function getList($page = 1, $step = 5){
     // Если неудалось получить текущую категорию.
     if(!$this->getCurrentCategory()){
       echo 'Ошибка получения данных!';
       exit;
     }

     // Вычисляет общее количество продуктов.
     $page = $page - 1;

     // Формируем фильтр для продуктов, по имеющимся категориям, внутри выбранной.
     $filter = '';
     foreach($this->categoryId as $catId){
       $filter .= ' OR c.id = '.$catId;
     }

     if('catalog' == $this->currentCategory['url']){

       // Запрос вернет все товары внутри выбраной категории, а также внутри вложеных в нее категорий.
       $sql = '
         SELECT  p.id
         FROM product p
         LEFT JOIN category c
          ON c.id = p.cat_id
       ';
       $result = DB::query($sql);
     }else{

       // Запрос вернет общее кол-во продуктов в выбранной категории.
       $sql = '
         SELECT  p.id
         FROM product p
         LEFT JOIN category c
           ON c.id = p.cat_id
         WHERE c.id = %d
       '.$filter;
       $result = DB::query($sql, end($this->categoryId));
     }

     // Макс количество продуктов.
     $count = ceil(DB::numRows($result) / $step);

     if(0 >= $page){
       $page = 0;
     }

     if($page >= $count){
       $page = $count - 1;
     }

     // Определяет нижнюю границу каталога.
     $lowerBound = $page * $step;

     // Определяет верхнюю границу каталога.
     if(0 > $lowerBound){
       $lowerBound = 0;
     }

     // Формирует страницу с продуктами.
     // Если категория не выбрана, то формирует запрос по всем имеющимся элементам.
     if(empty($this->categoryId)){
       $sql = '
         SELECT *
         FROM product
         ORDER BY id
         LIMIT %d , %d
       ';
       $result = DB::query($sql, $lowerBound, $step);

       // Иначе делает выборку только по выбранному разделу.
     }else{

       $filter = '';
       if(!empty($this->categoryId)){
         foreach($this->categoryId as $catId){
           $filter .= ' OR c.id = '.$catId;
         }
       }

       if('catalog' == $this->currentCategory['url']){
         $sql = '
           SELECT
             c.url as category_url,
             p.url as product_url,
             p.*
           FROM product p
           LEFT JOIN category c
             ON c.id = p.cat_id
           ORDER BY id
           LIMIT %d , %d
         ';
         $result = DB::query($sql, $lowerBound, $step);
       }else{
         $sql = '
           SELECT
             c.url as category_url,
             p.url as product_url,
             p.*
           FROM product p
           LEFT JOIN category c
             ON c.id = p.cat_id
           WHERE c.id = %d '.$filter.'
           ORDER BY id LIMIT %d , %d
         ';
         $result = DB::query($sql, $this->categoryId[0], $lowerBound, $step);
       }
     }

     // Если в разделе есть товары, то заполняет ими массив.
     if(DB::numRows($result)){
       while($row = DB::fetchAssoc($result)){
         $catalogItems[] = $row;
       }
     }

     $result = array('count' => $count, 'page' => $page, 'catalogItems' => $catalogItems);
     $args = func_get_args();
     return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
    }
}


<?php
/**
 * Класс Category - совершает все возможные операции с категориями товаров.
 * - Создает новую категорию;
 * - Удаляет категорию;
 * - Редактирует  категорию;
 * - Возвращает список только id всех вложеных категорий;
 * - Возвращает древовидный список категорий, пригодный для использования в меню;
 * - Возвращает массив id категории и ее заголовок;
 * - Возвращает иерархический массив категорий;
 * - Возвращает отдельные пункты списка заголовков категорий.
 */

class Category{

  // Массив категрорий.
  private $categories;

  public function __construct(){
    $result = DB::query('SELECT * FROM `category` WHERE visible = 0 ORDER BY id');

    while($row = DB::fetchArray($result)){
      $this->categories[] = $row;
    }
  }


  /**
   * Создает новую категорию.
   *
   * @param array $array  массив с даннми категории.
   * @return int|bool id новой категории.
   */
  public function addCategory($array){
    $result = false;
    $array['url'] = MG::translitIt($array['title']);

    if(strlen($array['url']) > 60){
      $array['url'] = substr($array['url'], 0, 60);
    }

    if(DB::buildQuery('INSERT INTO category SET ', $array)){
      $id = DB::insertId();
      $result = $id;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

  /**
   * Редактирует  категорию.
   *
   * @param int $id id редактируемой категории.
   * @param array $array  массив с даннми категории.
   * @return bool
   */
  public function editCategory($id, $array){
    $result = false;
    $array['url'] = MG::translitIt($array['title']);

    if(strlen($array['url']) > 60){
      $array['url'] = substr($array['url'], 0, 60);
    }

    if(DB::query('
      UPDATE category
      SET '.DB::buildPartQuery($array).'
      WHERE id = %d', $id)){
      $result = true;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

  /**
   * Удаляет категорию.
   *
   * @param int $id id удаляемой категории.
   * @return bool
   */
  public function delCategory($id){
    $categories = $this->getCategoryList($id);
    $categories[] = $id;

    foreach($categories as $categoryID){
      DB::query('
        DELETE FROM category
        WHERE id = %d
      ',
      $categoryID);
    }

    $args = func_get_args();
    $result = true;
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

  /**
   * Возвращает древовидный список категорий, пригодный для использования в меню.
   *
   * @param int $parent id категории, для которой надо вернуть список.
   * @param int $type тип списка (для публичной части, либо для админки).
   * @return string
   */
    public function getCategoryListUl($parent = 0, $type = 'public'){

    foreach($this->categories as $category){
		
		$im_seporator = '';
		if($category['seporator'] == '1')
			$im_seporator = '<li class = "im_separator"><a href=""></a></li>';

      if($parent == $category['parent']){

        if('admin' == $type){
          $print.= '<li><a href="#" rel="CategoryTree"
            id="'.$category['id'].'"
            parent_id="'.$category["parent"].'">'.$category['title'].'
          </a>';
        }
		
        if('public' == $type){
          $print.= '<li class = "cat'.$category['id'].'"><a href="'.SITE.'/'.$category['url'].'">'.$category['title'].'</a>';
		  //$print.= '<li><a href="#">'.$category['title'].'</a>';
        }

        foreach($this->categories as $sub_category){

          if($category['id'] == $sub_category['parent']){
            $flag = true;
            break;
          }
        }

        if($flag){
          $sub_menu = '
            <ul class="im_vert_menu">
              [li]
            </ul>';
          $li = $this->getCategoryListUl($category['id'], $type);
		  
			$li = $im_seporator.substr($li,0,(strrpos($li, $im_seporator) - 1)).'</ul>'; //Убираем последний сепоратор, добавляем верхний в подменю
			
          // Если вложенных категорий 0, то не создаем для них UL.
          $print .= strlen($li)>0 ? str_replace('[li]', $li, $sub_menu) : "";

          $print .= '</li>'.$im_seporator;
        }else{
          $print .= '</li>'.$im_seporator;
        }
      }
    }
	
	if(URL::getQueryParametr('category_id')){
		$catId = URL::getQueryParametr('category_id');
		$print = str_replace('cat'.$catId, 'active', $print);
	}
	
	$args = func_get_args();
	$result = $print;
	return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }


  /**
   * Возвращает массив вложенных категорий первого уровня.
   *
   * @param int $parent  id родительской категории.
   * @return string.
   */
  public function getChildCategoryIds($parentId = 0){
    $result = array();

    $res = DB::query('
      SELECT id
      FROM `category`
      WHERE parent = %d
      ORDER BY id
    ',
      $parentId);

    while($row = DB::fetchArray($res)){
      $result[] = $row['id'];
    }

     $args = func_get_args();
     return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }


  /**
   * Возвращает список только id всех вложеных категорий.
   *
   * @param int $parent id родительской категории
   * @return array
   */
  public function getCategoryList($parent = 0){

    foreach($this->categories as $category){
      if($parent == $category['parent']){
        $this->listCategoryId[] = $category['id'];
        $this->getCategoryList($category['id']);
      }
    }
    $args = func_get_args();
    $result = $this->listCategoryId;
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

  /**
   * Возвращает массив id категории и ее заголовок.
   *
   * @return array
   */
  public function getCategoryTitleList(){

    foreach($this->categories as $category){
      $titleList[$category['id']] = $category['title'];
    }

    $args = func_get_args();
    $result = $titleList;
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

  /**
   * Возвращает иерархический массив категорий.
   *
   * @param int $parent id родительской категории.
   * @return array
   */
  public function getHierarchyCategory($parent = 0){
    $catArray = array();
    foreach($this->categories as $category){
      if($parent == $category['parent']){
        $child = $this->getHierarchyCategory($category['id']);

        if(!empty($child)){
          $array = $category;
          $array['child'] = $child;
        }else{
          $array = $category;
        }

        $catArray[] = $array;
      }
    }
    $args = func_get_args();
    $result = $catArray;
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

  /**
   * Возвращает отдельные пункты списка заголовков категорий.
   *
   * @param array $arrayCategories массив с категориями.
   * @return string
   */
  public function getTitleCategory($arrayCategories, $selectCaegory = 0){
    global $lvl;

    foreach($arrayCategories as $category){
      $select = '';
      if($selectCaegory == $category['id']){
        $select = 'selected = "selected"';
      }
      $option .= '<option value='.$category['id'].' '.$select.' >';
      $option .= str_repeat('-', $lvl);
      $option .= $category['title'];
      $option .= '</option>';

      if(isset($category['child'])){
        $lvl++;
        $option .= $this->getTitleCategory($category['child']);
        $lvl--;
      }
    }
     $args = func_get_args();
     $result = $option;
     return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args );
  }

}
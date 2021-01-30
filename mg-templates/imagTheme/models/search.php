<?php
/**
 * Модель: search
 *
 * Класс Models_search реализует поиск товаров по подстроке.
 */
class Models_search{

  /**
   * Формирует массив ссылок результатов поиска продукта, учитывает выбранную страницу списка.
   * В полученный массив добавляет информацию для постраничной навигации.
   *
   * @param int $page  номер текущей страницы.
   * @param int $step  количество выводимых записей.
   * @param int $mgadmin флаг для построения ссылок навигации в админке и на сайте.
   * @return array
   */
	function getPageList($page = 1, $step = 5, $inputString){

		 $temparray = $this->getList($page, $step, $inputString);
		 $page = $temparray['page'];
		 $count = $temparray['count'];
		 $сatalogItems = $temparray['сatalogItems'];

	/**
	 * Делает постраничную навигацию.
	 * Устанавливаем активную страницу.
	 */
		$activPage = $page;

		if(1 < $count){
		// перебирает все страницы и формирует ссылки на них.
			for($page = 0; $page < $count; $page++){
				($activPage == $page) ? $class = 'activ' : $class = '';
				$pages .= '<a class = "'.$class.'" href = "'.SITE.'/search?p='.($page + 1).'&inputString='.$inputString.'">'.($page + 1).'</a>';
			}
			$pages = '<div class = "pagination">Страница '.($activPage + 1).' из '.($count).'</br></br> '.$pages.'</div>';
		}

	// Дописывает  к возвращаемому массиву информацию о пагинации.
		$сatalogItems['pagination'] = $pages;
		$сatalogItems['activPage'] = $activPage + 1;
		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $сatalogItems, $args);
	}

  /**
   * Формирует массив ссылок на категории продуктов.
   *
   * @param int $page номер текущей страницы.
   * @param int $step количество выводимых записей.
   * @return array
   */
    function getList($page = 1, $step = 5, $inputString){

	// Вычисляет общее количество продуктов.
		$page = $page - 1;

	// Запрос вернет общее кол-во продуктов в выбранной категории.
		if($inputString !== ''){
			$sql = "SELECT id FROM product WHERE name LIKE '%s'";
			$result = DB::query($sql,'%'.$inputString.'%');
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

		if($inputString !== ''){
			$sql = "SELECT p.id, p.name, p.desc, p.price, p.url as product_url, p.image_url, p.article, g.url as category_url, p.count_me, p.box
					FROM  product p
					join category g
					on p.cat_id = g.id
					WHERE p.name LIKE '%s' ORDER BY image_url DESC LIMIT %d , %d";
			$result = DB::query($sql,'%'.$inputString.'%',$lowerBound, $step);
		}

	// Если в разделе есть товары, то заполняет ими массив.
		if(DB::numRows($result)){
			while($row = DB::fetchAssoc($result)){
				$сatalogItems[] = $row;
			}
		}

		$result = array('count' => $count, 'page' => $page, 'сatalogItems' => $сatalogItems);
		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
    }
}


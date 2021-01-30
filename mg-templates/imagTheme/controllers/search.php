<?php
/**
 * Контроллер: search
 *
 * Класс Controllers_search обрабатывает AJAX запросы из публичной части.
 * - Отключает вывод шаблона;
 * - Подключает модель поиска
 * - Обрабатывает пагинацию
 */
class Controllers_search extends BaseController{

	function __construct(){
		MG::disableTemplate();

		// показать первую страницу выбранного раздела
		$page = 1;

		// сколько выводить на странице объектов
		$step = MG::getOption('countСatalogProduct');

		if(!is_numeric($step) || 1 > $step){
			$step = 1;
		}

	//Обработка строки запроса из строки поиска
		$inputString = '';
		if(isset($_REQUEST['inputString'])){
			$inputString = trimer($_REQUEST['inputString']);
		}

		//запрашиваемая страница
		if(isset($_REQUEST['p'])){
			MG::enableTemplate();
			$page = $_REQUEST['p'];
		}

		// модель каталога
		$model = new Models_search;

		// передаем номер требуемой страницы, и количество выводимых объектов
		$items = $model->getPageList($page, $step, $inputString);
		
		$pager = $items['pagination'];
		unset($items['pagination']);
		
		$activPage = $items['activPage'];
		unset($items['activPage']);

		$this->data = array(
			'items' => $items,
			'titeCategory' => 'Результаты поиска',
			'pager' => $pager,
			'inputString' => $inputString,
			'activPage' => $activPage
		);
	}
 
}
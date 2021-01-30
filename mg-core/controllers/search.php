<?php
/**
 * Контроллер: search
 *
 * Класс Controllers_search обрабатывает AJAX запросы из публичной части.
 * - Отключает вывод шаблона;
 * - Осуществляет поиск товаров
 */
class Controllers_search extends BaseController{

	function __construct(){
		MG::disableTemplate();
		echo ';jgf';
	}
 
}
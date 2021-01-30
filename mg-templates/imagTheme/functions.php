<?php  

////////////////////////////////Вызовы функций////////////////////////////////////////
mgAddAction('mg_titlepage', 'myTitle');
enableCookInSession();


//////////////////////////////Функции/////////////////////////////////////////////////

//Обработка строки поиска 
	function trimer($queryString){
		mb_internal_encoding("UTF-8");
	//заменяет всё на пробелы, кроме букв
		$queryString = preg_replace('/[^a-zа-яё]/iu','',$queryString);
	//заменяет подряд идущие пробелы на один пробел
		$queryString = preg_replace("/ +/"," ",$queryString);
	//обрезаем крайние пробелы
		//$QueryString = trim ($QueryString);
		return $queryString;
	}

	function myTitle(){ 
	  setOption('title', ' Доставка на дом | '.getOption('title'));
	}

//Ставит флажок запрещающий запись куков в сессию. Нужно для добавления товаров в чек, через Ajax
  function disableCookInSession() {
    $_SESSION['noCookInSesson'] = true;
  }
  
//Ставит флажок разрешиющий запись куков в сессию. Нужно для добавления товаров в чек, через Ajax
  function enableCookInSession() {
    $_SESSION['noCookInSesson'] = false;
  }

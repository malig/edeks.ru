<?php

/**
 * Модель: Personal
 *
 */

class Models_Personal{

	public function getPageList($page = 1, $step = 5, $mgadmin = false, $userID){

		$temparray = $this->getList($page, $step, $userID);
		$page = $temparray['page'];
		$count = $temparray['count'];
		$orderItems = $temparray['orderItems'];

		$activPage = $page;


		if(1 < $count){
			for($page = 0; $page < $count; $page++){
				($activPage == $page) ? $class = 'activ' : $class = '';
				$pages .= '<a rel="pagination_order" page = "'.($page + 1).'" class = "'.$class.'" href = "'.SITE.'/personal?p='.($page + 1).'">'.($page + 1).'</a>';
			}
			$pages = '<div class = "pagination">Страница '.($activPage + 1).' из '.($count).'</br></br>'.$pages.'</div>';
		}

		$orderItems['pagination'] = $pages;
		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $orderItems, $args);
	}

/*******************************************************************************/
	private function getList($page = 1, $step = 5, $userID){

		$page = $page - 1;

		$sql = '
		 SELECT id
		 FROM `order`
		 WHERE user_id = '. $userID .'
		';
		
		$result = DB::query($sql);

		$count = ceil(DB::numRows($result) / $step);

		if(0 >= $page){
			$page = 0;
		}

		if($page >= $count){
			$page = $count - 1;
		}

		$lowerBound = $page * $step;

		if(0 > $lowerBound){
			$lowerBound = 0;
		}
		
		$sql = '
		 SELECT *
		 FROM `order`
		 WHERE user_id = '. $userID .'
		 ORDER BY date DESC
		 LIMIT %d , %d
		';
		$result = DB::query($sql, $lowerBound, $step);		

		if(DB::numRows($result)){
			while($row = DB::fetchAssoc($result)){
				$orderItems[] = $row;
			}
		}

		$result = array('count' => $count, 'page' => $page, 'orderItems' => $orderItems);
		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
	}
	
	public function changePass($newPass, $id, $forgotPass = false){
	$userData = array(
	  'pass' => $newPass,
	);
	$registration = new Models_Registration;

	if($err = $registration->validPass($userData, 'pass')){
	  $msg = $err;
	}else{
	  $userData['pass'] = crypt($userData['pass']);
	  USER::update($id, $userData, $forgotPass);
	  $msg = "Пароль изменен";
	}

	$args = func_get_args();
	return MG::createHook(__CLASS__."_".__FUNCTION__, $msg, $args);
	}			

}
<?php

class Models_Pagination{
	
	public $filterId;

	function getPageList($page = 1, $step = 5, $mgadmin = false){

		$temparray = $this->getList($page, $step);
		$page = $temparray['page'];
		$count = $temparray['count'];
		$orderItems = $temparray['orderItems'];

		$activPage = $page;


		if(1 < $count){
			for($page = 0; $page < $count; $page++){
				($activPage == $page) ? $class = 'activ' : $class = '';
				$pages .= '<a rel="pagination_order" page = "'.($page + 1).'" class = "'.$class.'" href = "#">'.($page + 1).'</a>';
			}
			$pages = '<div class = "pagination">Страница '.($activPage + 1).' из '.($count).'</br></br>'.$pages.'</div>';
		}

		$orderItems['pagination'] = $pages;
		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $orderItems, $args);
	}

/*******************************************************************************/
	function getList($page = 1, $step = 5){
		
		$filter = " WHERE close = 'N' ";
		switch ($this -> filterId) {
			case "0":
				$filter = "";
				break;
			case "1":
				$filter = " WHERE close = 'N' ";
				break;
		}

		$page = $page - 1;

		$sql = '
		 SELECT id
		 FROM `order`
		'.$filter;
		
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
		 FROM `order`'
		 .$filter.
		 'ORDER BY close ASC, date ASC
		 LIMIT %d , %d
		';
		$result = DB::query($sql, $lowerBound, $step);		

		$sqlUser = '
		 SELECT count(*) as count
		 FROM `order`
		 WHERE user_id = %d';

		if(DB::numRows($result)){
			while($row = DB::fetchAssoc($result)){
				if ($row['user_id'] != 0) {
					$resultUser = DB::query($sqlUser, $row['user_id']);
					if($rowUser = DB::fetchAssoc($resultUser)){
						$row['countOrdersOfUser'] = $rowUser['count'];
					}
				}else{
					$row['countOrdersOfUser'] = 0;
				}
				$orderItems[] = $row;
			}
		}

		$result = array('count' => $count, 'page' => $page, 'orderItems' => $orderItems);
		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
	}
}


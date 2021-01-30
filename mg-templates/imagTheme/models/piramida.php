<?php

/**
 * Модель: Personal
 *
 */

class Models_Piramida{
	public $step = 1;
	public $bank = array(
				'1' => 20,
				'2' => 30,
				'3' => 35,
				'4' => 38,
				'5' => 40
			);

	public function toChargePrize($userId){

		
		
		if($userInfo = USER::getUserById($userId)){
			
			$prize = $this->bank[$this->step] + $userInfo->prize;
			
			USER::setUserPrize($prize, $userId);
			
			if($userInfo->baas !== '0' && $this->step < 5){
				$this->step = $this->step + 1;
				$this -> toChargePrize($userInfo->baas);
			}
		}	


		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
	}
	
	public function getFirstParent($orderId){
		$result = false;
		//Получаем инфу о заказе
		$orderInfo = USER::getUserInfoByOrderId($orderId);
		
		//Если заказчик зарегистрирован, получаем инфу о нём
		if($orderInfo ->user_id !== "0"){
			
			$userInfo = USER::getUserById($orderInfo ->user_id);
			
		//Если за заказ ещё не начисляли приз и у заказчика есть родитель, то отдаем родителя и устанавливаем флаг начисления в труе	
			if($orderInfo ->is_prized == "N" && $userInfo -> baas !== "0"){
				USER::setIsPrised($orderId);
				$result = $userInfo -> baas;
			}else{
				$result = false;
			}
		}else{
			$result = false;
		}		

		$args = func_get_args();
		return MG::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
	}	

}
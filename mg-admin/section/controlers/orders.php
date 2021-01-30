<?php
//функция сортировки массива
function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strnatcmp($a[$key], $b[$key]);
    };
}
 
$result = DB::query('SELECT  id  FROM `order` LIMIT 1');

$model = new Models_Pagination;

$model->filterId = URL::get('filter_id');
$this->filter = $model->filterId;

$arOrder = array();
$arOrder = $model->getPageList(URL::get('page'), 3, true);
$pagination = $arOrder['pagination'];
unset($arOrder['pagination']);

$this->chek = $arOrder;

$this->order = $arOrder;

if(!empty($result)){
    $tableOrders = '
        <table id="table_order">
            <tr>
                <th>№</th>
                <th>Имя</th>
                <th>Тел.</th>
                <th>Дата</th>
                <th>Сумма</th>
                <th>В работе</th>
                <th>Закрыт</th>
                <th style="display:none;">Состав заказа</th>
                <th>Адрес</th>
            </tr>
    ';

    $odd = 1;
  
    $sale = MG::getOption('sale');
  
    foreach($arOrder as $order){
        $odd = !$odd;
        $odd ? $rowColor = 'odd' : $rowColor = 'event';

        $orderItems = json_decode(str_replace('%%', '%',$order['order_content']),true);
    	
    	usort($orderItems, build_sorter('cat_id')); //Сортировка по номеру категории
    	
    	$arAdr = explode('+',$order['adres']);
    	
    	$priceDelivery = MG::getOption('price_delivery');
	
    	if($order['has_sale'] == 'Y'){
    		$priceDelivery = $priceDelivery - $sale;
    	}
	
    	$totalSummm = $order['summ'] + $priceDelivery;
    	$Sum = $totalSummm;
	
        $ballBlock = "";

    	if($order['user_id'] != "0"){
    		$userInfo = USER::getUserById($order['user_id']);
    		$ballBlock = '<tr>
                <td colspan = 5>БОНУСЫ</td>
    			<td align = "center">'.$userInfo->prize.'</td>
              </tr>';
    		  
    		if($userInfo->prize <= $totalSummm){
    			$totalSummm = $totalSummm - $userInfo->prize;
    		}else{
    			$totalSummm = 0;
    		}	  
    	}	
	
        $contact = '
            <table style="width:700px;" border="1">
                <tr>
                    <td>Заказ номер: </td>
                    <td id="order_id"></td>
                </tr>
                <tr>
                    <td>Заказчик:</td>
                    <td>'.$order['name'].' ('.$order['countOrdersOfUser'].')</td>
                </tr>
                <tr>
                    <td>Электронный адрес:</td>
                    <td>'.$order['email'].'</td>
                </tr>
                <tr>
                    <td>Телефон:</td>
                    <td>'.$order['phone'].'</td>
                </tr>
                <tr>
                    <td>Адрес доставки:</td>
                    <td>
                        <div>'.$arAdr[0].'</div>
                        <a id = "adress_link" target="_blank" href="http://maps.yandex.ru/?text='.urlencode($order['adres']).'">'.
                        $arAdr[1].' '.
                        $arAdr[2].' '.
                        $arAdr[3].
                        '</a>
                    </td>
                </tr>
                <tr>
                    <td>Сумма к оплате:</td>
                    <td style="color:red">'.$totalSummm.' руб.</td>
                </tr>
            </table>';

        if(count($orderItems) > 0){
            $printOrderItems = '
                <table  style="width:700px;" class="product_price" border="1">
                    <tr >
                        <th>Icon</th>
                        <th>Товар</th>
                        <th>Артикул</th>
                        <th>Кол-во</th>
                        <th>Цена</th>
                        <th>Сумма</th>
                    </tr>
            ';


$chekString = '';
$key = 1;
            foreach($orderItems as $items){

switch ($key) {
    case '1':
        $key++;
        $chekString .= '<div style = "float:left;width:33%">
                    <div>'.$items['name'].'</div>
                    <div>'.
                        'арт:'.$items['article'].'кол-во:'.$items['count'].
                        'цена:'.$items['price'].'сум:'.$items['summm'].
                    '</div>
                </div>';
        break;
    case '2':
        $key++;
        $chekString .= '<div style = "float:left;width:33%">
                    <div>'.$items['name'].'</div>
                    <div>'.
                        'арт:'.$items['article'].'кол-во:'.$items['count'].
                        'цена:'.$items['price'].'сум:'.$items['summm'].
                    '</div>
                </div>';
        break;
    case '3':
        $key = 1;
        $chekString .= '<div style = "border-bottom:1px black solid">
                    <div>'.$items['name'].'</div>
                    <div>'.
                        'арт:'.$items['article'].'кол-во:'.$items['count'].
                        'цена:'.$items['price'].'сум:'.$items['summm'].
                    '</div>
                </div>
                <div style = "clear:both"></div>';
        break;
}

if ($items['img_url'] == '') {
    $img_url = 'none.png';
}else{
    $img_url = $items['img_url'];
}
                $printOrderItems .= '
                    <tr>
                        <td>
                            <div id = "izoDiv">
                                <image onload="autoSize(this,60)"  src=\''.SITE.'/uploads/'.$img_url.'\'  />
                            </div>
                        </td>
                        <td>'.$items['name'].'</td>
                        <td align = "center">'.$items['article'].'</td>
                        <td align = "center">'.$items['count'].'</td>
                        <td align = "center">'.$items['price'].'</td>
                        <td align = "center">'.$items['summm'].'</td>
                    </tr>
                ';
            }
	  
            $printOrderItems .= '
                    <tr>
                        <td colspan = 5>СТОИМОСТЬ ДОСТАВКИ</td>
                        <td align = "center">'.$priceDelivery.'</td>
                    </tr>
                    <tr>
                        <td colspan = 5>СУММА С УЧЕТОМ ДОСТАВКИ</td>
                        <td align = "center"><strong>'.$Sum.'</strong></td>
                    </tr>'.$ballBlock;		  		  

            $printOrderItems .= '</table></br>
                <div id = "summ">Итого: '.$totalSummm.'</div>
                <div style = "clear:both"></div>
                <div class = "pagePrint" style="width:700px; height:950px;font-size:10;">
                    <div style = "border-bottom:1px black solid">Номер:'.
                        $order["id"].'| Заказчик:'.$order["name"].'| mail: '.$order["email"].'| Телефон:'.$order["phone"].
                        '| Адрес: '.$arAdr[0].' '.$arAdr[1].' '.$arAdr[2].' '.$arAdr[3].'| Сумма товаров:'.$order['summ'].
                        '| Цена доставки:'.$priceDelivery.'| Бонусы:'.$userInfo->prize.'| Итого к оплате: '.$totalSummm
                    .'</div>'.    
               $chekString
                .'<div style = "clear:both"></div></div>';
        }
	
        $printColor = "";

        if('Y' == $order['print']){
            $orderPrint = 'Да';
        }else{
            $orderPrint = 'Нет';
            $printColor = 'printcolor';
        }

        $classColor = "";

        if('Y' == $order['close']){
            $classColor = 'trcolor';
            $orderСlose = 'Да';
        }else{
            $orderСlose = 'Нет';
        }

        $tableOrders .= '
            <tr class="'.$printColor.' '.$classColor.' '.$rowColor.'" order_id="'.$order['id'].'" >
                <td >'.$order['id'].'</td>
                <td >'.$order['name'].'</td>
                <td >'.$order['phone'].'</td>
                <td >'.date("d/m/y G:i",strtotime($order['date'])).'</td>
                <td >'.$totalSummm.' руб.</td>
                <td id="print">'.$orderPrint.'</td>
                <td id="close">'.$orderСlose.'</td>
                <td class="order_content" style="display:none;">'.$printOrderItems.'</td>
                <td class="contactHide" style="display:none;">'.$contact.'</td>
                <td class = "fullAdr">'.'Томск+'.$arAdr[0].'+'.$arAdr[1].'+'.$arAdr[2].'+'.$arAdr[3].'</td>
            </tr>';
    }

    $tableOrders .= '</table>';
    $this->tableOrders = $tableOrders;
    $this->pagination = $pagination;
}else{
    echo 'Пока не поступило ни одного заказа.';
}

/**     
 * Функция определения способа оплаты исходя из данных БД
 *
 * @param string Способ оплаты в БД
 * @return string Перобразованная в человеческий вид строка
 */
function findPayment($payment){

  switch($payment){
    case 'webmoney':
      return 'WebMoney';
    case 'yandex':
      return 'Яндекс.Деньги';
    case 'platezh':
      return 'Наложенный платеж';
    case 'nal2kurier':
      return 'Наличные (курьеру)';
    default:
      return 'Другой способ оплаты';
  }
}

/**
 * Функция определения способа доставки исходя из данных БД
 *
 * @param string Способ доставки в БД
 * @return string Перобразованная в человеческий вид строка
 */
function findDelivery($delivery){

  switch($delivery){
    case 'kurier':
      return 'Курьером';
    case 'pochta':
      return 'Почтой';
    default:
      return 'Другой способ доставки';
  }
}
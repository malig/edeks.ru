<?php
/**
 *Список пользователей в админке
 */

$result = DB::query('SELECT  id  FROM `user` LIMIT 1');
$model = new Models_Users;

if(URL::getQueryParametr('filter')){
	$model->filter = URL::getQueryParametr('filter');
}

$arUsers = array();
$arUsers = $model->getPageList(URL::get('page'), 3, true);
$pagination = $arUsers['pagination'];
unset($arUsers['pagination']);

if(!empty($result)){
  $tableOrders = '
    <table id="im_table_user">
      <tr>
        <th>id</th>
        <th>email</th>
        <th>Имя(name)</th>
		<th>Адрес(address)</th>
        <th>Телефон(phone)</th>
        <th>Дата(date_add)</th>
        <th>Легал(legal)</th>
        <th style="display:none;">Состав заказа</th>
        <th>Бонус(prize)</th>
		<th>Предок(baas)</th>
		<th>role</th>
      </tr>
    ';

  $odd = 1;
  
  foreach($arUsers as $user){
    $odd = !$odd;
    $odd ? $rowColor = 'odd' : $rowColor = 'event';
	
    $contact = '
      <table style="width:700px;" border="1">
		<tr>
			<td>Id: </td><td id = "userID">'.$user['id'].'<input type="hidden" name="id" value = "'.$user['id'].'"/> </td>
		</tr>
        <tr>
          <td>Mail: </td><td><input style = "width:500px" type="text" name="email" value = "'.$user['email'].'"/> </td>
        </tr>
        <tr>
          <td>Имя: </td><td><input style = "width:500px" type="text" name="name" value = "'.$user['name'].'"/> </td>
        </tr>
        <tr>
          <td>Адрес: </td><td><input style = "width:500px" type="text" name="address" value = "'.$user['address'].'"/> </td>
        </tr>
        <tr>
          <td>Телефон: </td><td><input style = "width:500px" type="text" name="phone" value = "'.$user['phone'].'"/> </td>
        </tr>
        <tr>
          <td>Активация: </td><td><input style = "width:500px" type="text" name="legal" value = "'.$user['legal'].'"/> </td>
        </tr>
        <tr>
          <td>Бонусы: </td><td><input style = "width:500px" type="text" name="prize" value = "'.$user['prize'].'"/> </td>
        </tr>
        <tr>
          <td>Предок: </td><td><input style = "width:500px" type="text" name="baas" value = "'.$user['baas'].'"/> </td>
        </tr>
        <tr>
          <td>Права: </td><td><input style = "width:500px" type="text" name="role" value = "'.$user['role'].'"/> </td>
        </tr>
      </table>';

    $tableOrders .= '
     <tr class="'.$rowColor.'" user_id="'.$user['id'].'" >
        <td >'.$user['id'].'</td>
        <td >'.$user['email'].'</td>
        <td >'.$user['name'].'</td>
		<td >'.$user['address'].'</td>
        <td >'.$user['phone'].'</td>
        <td >'.date("d/m/y",strtotime($user['date_add'])).'</td>
        <td >'.$user['legal'].'</td>
        <td class="user_content" style="display:none;">'.$printOrderItems.'</td>
        <td class="contactHide" style="display:none;">'.$contact.'</td>
        <td >'.$user['prize'].'</td>
		<td >'.$user['baas'].'</td>
		<td >'.$user['role'].'</td>
      </tr>';
  }

  $tableOrders .= '</table>';
  $this->tableOrders = $tableOrders;
  $this->pagination = $pagination;
}else{
  echo 'Пока не поступило ни одного заказа.';
}

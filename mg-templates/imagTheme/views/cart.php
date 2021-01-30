<?php mgTitle('Корзина');?>
<h1>Корзина</h1>

<?
//echo '<pre>';
//print_r($_SESSION);
//echo '</pre>';
?>

<?php if($data['isEmpty']): ?>
  <form action="<?php echo SITE?>/cart" method="post">
	  <table class="table_cart">
		<tr>
		<th>№</th>
		<th>Наименование</th>
		<th>Стоимость</th>
		<th>Количество</th>
		<th>Сумма</th>
		<th>Удалить</th>
		</tr>
    <?php 
	$priseDelivery = MG::getOption('price_delivery');
	$finishSumm = $data['totalSumm'] + $priseDelivery;
    $i = 1;
    foreach($data['productPositions'] as $product):?>
      <tr class = "row">
      <td><?php echo $i++ ?></td>
      <td><?php echo $product['name'] ?></td>
      <td><?php echo $product['price'] ?> руб.</td>
      <td>
        <input type="text" style="text-align:center" size="3" name="item_<?php echo $product['id'] ?>" value = "<?php echo $_SESSION['cart'][$product['id']]?>" />
      </td>
      <td> <?php echo ($_SESSION['cart'][$product['id']] * $product['price'])?> руб.</td>
      <td>
	    <input type="checkbox"  name="del_<?php echo $product['id'] ?>">
	  </td>
      </tr>
    
      <?php endforeach;?>
	  
	  <tr class = "row">
      <td><?php echo $i++ ?></td>
      <td style = "text-align:left;padding-left:5px;">СТОИМОСТЬ ДОСТАВКИ</td>
      <td style = "border-left:0px !important;"></td>
      <td style = "border-left:0px !important;">
      </td>
      <td><?=$priseDelivery?> руб.</td>
      <td>
	  </td>
      </tr>
	  
<?//Если пользователь авторизован, считаем балы
if($userInfo = $data['userInfo']){
	
	if($data['userInfo']->prize <= $finishSumm){
		$finishSumm = $finishSumm - $data['userInfo']->prize;
	}else{
		$finishSumm = 0;
	}		
?>
	<tr class = "row">
		<td><?php echo $i++ ?></td>
		<td style = "text-align:left;padding-left:5px;">БОНУСЫ</td>
		<td style = "border-left:0px !important;"></td>
		<td style = "border-left:0px !important;"></td>
		<td><?=$data['userInfo']->prize ?></td>
		<td></td>
	</tr>
	<tr class="totalRow">
		<td colspan = 3></td>
		<td>Сумма с доставкой: </td>
		<td style="text-align:center"><strong><?echo $data['totalSumm'] + $priseDelivery;?> руб.</strong></td>
		<td></td>
	</tr>
<?
}
?>	  
    
      <tr class="totalRow">
        <td colspan = 3>
			<input type="submit" name="refresh" class="button" value="Пересчитать"  style="margin:10px; float:left" />
		</form>
		
		  <form action="<?php echo SITE?>/order" method="post" style="margin-left:0px;">
			<input type="submit" name="order" value="Далее" style="margin:10px" class="button" />
		  </form>
		
		</td>
        <td>Сумма с учетом бонуса: </td>
        <td style="text-align:center">
			<strong>
				<span style="color: #00ABC2"><?php echo $finishSumm;?> руб. </span>
			</strong>
		</td>
        <td></td>
      </tr>
      </table>
	



<?php else : ?>
<div class="mainCont">
  <h3>Ваша корзина пуста!</h3>
</div>
<?php endif; ?>

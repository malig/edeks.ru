<?php mgTitle('Корзина');?>
<h1>Корзина</h1>

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
    
      <tr class="totalRow">
        <td colspan = 3></td>
        <td>К оплате: </td>
        <td><strong>
          <span style="color: #00ABC2"><?php echo $data['totalSumm'] ?> руб. </span>
        </strong></td>
        <td></td>
      </tr>
      </table>
	
    <input type="submit" name="refresh" class="btn" value="Пересчитать"  style="margin:10px" />
  </form>
  <form action="<?php echo SITE?>/order" method="post" style="margin-left:590px;">
    <input type="submit" name="order" value="Оформить заказ" style="margin:10px" class="btn" />
  </form>

<?php else : ?>
<div class="mainCont">
  <h3>Ваша корзина пуста!</h3>
</div>
<?php endif; ?>

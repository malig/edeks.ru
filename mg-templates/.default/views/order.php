<?php mgTitle('Оформление заказа');?>
<?php
if($data['dislpayForm']):
?>
  <h1>
	Оформление заказа
	<div class="errorSend">
		<?php
		if($data['error']){
		  echo $data['error'];
		}
		?>
	</div>
  </h1>
<?php else : ?>
  <h1>Оплата заказа</h1>
  <?php mgTitle('Оплата заказа');?>
<?php endif; ?>

<?php

if($data['dislpayForm']):
?>

<div class="mainCont">
<a class="arrowLeft" href="<?php echo SITE?>/cart">Назад в корзину</a>
  <form action="<?php echo SITE?>/order" style="margin-top: 10px;" method="post">
    <table class="table_order_form">
      <tr>
	  <td>
	  <table class="table_order_form">
	  <tr>
        <td>Ф.И.О.</td>
        <td><input type="text" name="fio" value="<?php echo $_POST['fio'] ?>"/></td>
      </tr>
      <tr>
        <td>E-mail<span style="color: red;">*</span></td>
        <td><input type="text" name="email" value="<?php echo $_POST['email'] ?>"/></td>
      </tr>
      <tr><td>Телефон</td>
        <td><input type="text" name="phone" value="<?php echo $_POST['phone'] ?>"/></td>
      </tr>
      <tr><td>Адрес</td>
        <td><textarea name="address"><?php echo $_POST['address'] ?></textarea></td>
		</table>
		</td>
		<td width="50"></td>
		<td style="vertical-align: top;">
		  <strong>Доставка</strong>
		<table class="table_order_form">
		  <tr>
			<td>Курьером</td>
			<td><input type="radio" name="delivery" value="kurier"></td>
		  </tr>
		  <tr>
			<td>Почтой</td>
			<td><input type="radio" checked="checked" name="delivery" value="pochta"></td>
		  </tr>
		</table>
		</td>
		<td width="50"></td>
		<td style="vertical-align: top;">
		  <strong>Способ оплаты</strong>
			<table class="table_order_form">
			  <tr>
				<td>WebMoney</td><td><input type="radio" name="payment" value="webmoney"></td>
			  </tr>
			  <tr>
				<td>Яндекс.Деньги</td><td><input type="radio"  name="payment" value="yandex"></td>
			  </tr>
			  <tr>
				<td>Наложенный платеж</td>
				<td><input type="radio" checked="checked" name="payment" value="platezh"></td>
			  </tr>
			  <tr>
				<td>Наличные (курьеру)</td>
				<td><input type="radio" name="payment" value="nal2kurier"></td>
			  </tr>
			</table>
		</td>
      </tr>
    </table>
		<input type="submit" name="toOrder" class="btn" value="Оформить заказ">
  </form>
</div>
  <?php else:?>
  <?php if(!$data['pay'] && $data['payment'] == 'fail'): ?> 
  <div class="mainCont"><span style="color:red"><?php echo $data['message']; ?></span></div>
  <?php else:?>
  <div class="mainCont"><span style="color:green">Ваша заявка <strong>№ <?php echo $data['id']?></strong> принята!</span>
  <hr>
  <p>Оплатить заказ <b>№ <?php echo $data['id']?> </b> на сумму <b><?php echo $data['summ']?></b> руб. </p></div>
   <?php endif;?>
<?php
  if('webmoney' == $data['pay']){ 
    //Подключаем файл оплаты webmoney 
    include('mg-pages/webmoney/pay.php');
  }

  if('yandex' == $data['pay']){
    // Подключаем файл оплаты yandex 
    include('mg-pages/yandex/pay.php');
  }
endif;

<?php mgTitle('Оформление заказа');?>
<?php
if($data['dislpayForm']):
?>
  <h1>
	Оформление заказа
	<div class="errorSend">
		<?php
		if($data['error']){
		  echo $data['error']['err'];
		}
		?>
	</div>
  </h1>
<?php else : ?>
  <h1>Оплата заказа</h1>
  <?php mgTitle('Оплата заказа');?>
<?php endif; ?>

<?php

$select['Проезд'] = "";
$select['Улица'] = "";
$select['Проспект'] = "";
$select['Переулок'] = "";

if(isset($_SESSION['user'])){
	$_POST['fio'] = $_SESSION['user']->name;
	$_POST['email'] = $_SESSION['user']->email;
	$_POST['phone'] = $_SESSION['user']->phone;

	$arAdr = explode('+',$_SESSION['user']->address);
	$arDom= explode('/',$arAdr[2]);

	$select[$arAdr[0]] = "selected";
	
	$_POST['nameStreet'] = $arAdr[1];
	$_POST['dom'] = str_replace("д.","",$arDom[0]);
	$_POST['drob'] = $arDom[1];
	$_POST['room'] = str_replace("кв.","",$arAdr[3]);
}

if($data['dislpayForm']):
?>

<div class="mainCont">
	<a class="arrowLeft button" href="<?php echo SITE?>/cart">Назад в корзину</a>
	</br><div class = "mainContText">Доставка осуществляется только по городу <strong>ТОМСК</strong>!</div></br>
	<form action="<?php echo SITE?>/order" style="margin-top: 10px;" method="post">
		<table class="enter">
			<tr>
				<td><label>Имя</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['fio']."</div>"?>
					<input type="text" name="fio" value="<?php echo $_POST['fio'] ?>"/>
				</td>
			</tr>
			<tr>
				<td><label>E-mail</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['email']."</div>"?>
					<input type="text" name="email" value="<?php echo $_POST['email'] ?>"/>
				</td>
			</tr>
			<tr>
				<td><label>Телефон</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['phone']."</div>"?>
					<input type="text" name="phone" value="<?php echo $_POST['phone'] ?>"/>
				</td>
			</tr>
			<tr>
				<td><label>Адрес</label><span style="color: red;">*</span></td>
				<td id = "adress">
					<?php echo "<div style = 'color:red;'>".$data['error']['nameStreet']."</div>"?>
					<?php echo "<div style = 'color:red;'>".$data['error']['dom']."</div>"?>
					<?php echo "<div style = 'color:red;'>".$data['error']['room']."</div>"?>
					<?php echo "<div style = 'color:red;'>".$data['error']['drob']."</div>"?>
					<select name="streetDom" style="float:left;">
						<option value="Улица" <?echo $select['Улица']?>>Улица</option>
						<option value="Проспект" <?echo $select['Проспект']?>>Проспект</option>
						<option value="Переулок" <?echo $select['Переулок']?>>Переулок</option>
						<option value="Проезд" <?echo $select['Проезд']?>>Проезд</option>
					</select>
			
					<input type="text" style="width:183px;margin-left:15px" name="nameStreet" value="<?php echo $_POST['nameStreet'] ?>"/>
					<label>Дом:&nbsp;</label>
					<input type="text" style="width:40px;" name="dom" value="<?php echo $_POST['dom'] ?>"/>
					<label style="margin-left:0">&nbsp;&#47;&nbsp;</label>
					<input type="text" style="width:20px;" name="drob" value="<?php echo $_POST['drob'] ?>"/>
					<label>Квартира:&nbsp;</label>
					<input type="text" style="width:40px;" name="room" value="<?php echo $_POST['room'] ?>"/>
				</td>
			<!--tr>
				<td><label>Примечание</label></td>
				<td><input type="text" name="node" value="<?php echo $_POST['node'] ?>"/></td>
			</tr-->		
			<tr>
				<td><label>Номер с VIP карты:</label></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['prize_key']."</div>"?>
					<input type="text" name="prize_key" value="<?php echo $_POST['prize_key'] ?>"/>
				</td>
			</tr>	
		</table>
		
		<input type="submit" name="toOrder" class="button" value="Отправить заказ">
	</form>
</div>
  <?php else:?>
  <?php if(!$data['pay'] && $data['payment'] == 'fail'): ?> 
  <div class="mainCont"><span style="color:red"><?php echo $data['message']; ?></span></div>
  <?php else:?>
  <div class="mainCont"><span style="color:#9CB800">Ваша заявка <strong>№ <?php echo $data['id']?></strong> принята!</span>
  <hr>
  <!--p   style="color:#808080">Оплатить заказ <b>№ <?php echo $data['id']?> </b> на сумму <b><?php echo $data['summ']?></b> руб. </p--></div>
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

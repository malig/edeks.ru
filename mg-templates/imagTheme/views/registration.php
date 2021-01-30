<?php mgTitle('Регистрация'); ?>

<a style = "float: left;" href="<?php echo SITE?>">Главная страница / </a>
<a style = "float: left; margin-left:5px;" href="">Регистрация</a>

<div class="mainCont">
	<div class="mainContText">
		<?php
		if($data['isRegistered']){
		  echo "<p> Вы успешно зарегистрировались! Для активации пользователя Вам необходимо перейти по ссылке высланной на Ваш электронный адрес</p></br>";
		}
		else{
		?>

		<?php	
			if($data['error']){
				echo "<p style = 'color:red;'>".$data['error']['err']."</p></br>";
			}
		?>
	</div>

    <form action = "<?php echo SITE?>/registration" method = "POST">
		<table class = "enter">
			<tr>
				<td><label>E-mail:</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['errmail']."</div>"?>
					<input type = "text" name = "email" value = "<?php echo $_POST['email']?>" /> 
				</td>
			</tr>
			<tr>
				<td><label>Пароль:</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['errpass']."</div>"?>
					<input type="password" name="pass" /> 
				</td>
			</tr>
			<tr>
				<td><label>Подтвердите пароль:</label><span style="color: red;">*</span></td>
				<td><input type="password" name="pass2" /></td>
			</tr>
			<tr>
				<td><label>Имя:</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['errname']."</div>"?>
					<input type="text" name="name" value = "<?php echo $_POST['name']?>" /> 
				</td>
			</tr>
			<tr>
				<td><label>Телефон:</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['errphone']."</div>"?>
					<input type="text" name="phone"  value = "<?php echo $_POST['phone']?>"/> 
				</td>
			</tr>
			<tr>
				<td><label>Адрес:</label><span style="color: red;">*</span></td>
				<td id = "adress">
					<?php echo "<div style = 'color:red;'>".$data['error']['nameStreet']."</div>"?>
					<?php echo "<div style = 'color:red;'>".$data['error']['dom']."</div>"?>
					<?php echo "<div style = 'color:red;'>".$data['error']['room']."</div>"?>
					<?php echo "<div style = 'color:red;'>".$data['error']['drob']."</div>"?>
					<select name="streetDom" style="float:left;">
						<option value="Улица" selected>Улица</option>
						<option value="Проспект">Проспект</option>
						<option value="Переулок">Переулок</option>
						<option value="Проезд">Проезд</option>
					</select>
			
					<input type="text" style="width:183px;margin-left:15px" name="nameStreet" value="<?php echo $_POST['nameStreet'] ?>"/>
					<label>Дом:&nbsp;</label>
					<input type="text" style="width:40px;" name="dom" value="<?php echo $_POST['dom'] ?>"/>
					<label style="margin-left:0">&nbsp;&#47;&nbsp;</label>
					<input type="text" style="width:20px;" name="drob" value="<?php echo $_POST['drob'] ?>"/>
					<label>Квартира:&nbsp;</label>
					<input type="text" style="width:40px;" name="room" value="<?php echo $_POST['room'] ?>"/>
				</td>
			</tr>
			<tr>
				<td><label> </label></td>
				<td><img style="margin-top: 5px; margin-left:15px; border: 1px solid gray; background: url('<?php echo PATH_TEMPLATE ?>/images/cap.png');" src = "captcha.html" width="140" height="36"></td>
			</tr>		
			<tr>
				<td><label>Введите текст с картинки:</label><span style="color: red;">*</span></td>
				<td>
					<?php echo "<div style = 'color:red;'>".$data['error']['errcapcha']."</div>"?>
					<input type="text" name="capcha" class="captcha"> 
				</td>
			</tr>		
		</table>
      <input type = "submit" class="button" value = "Зарегистрироваться" />
    </form>
  </div>

<?php } ?>
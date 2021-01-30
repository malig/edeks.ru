<?php mgTitle('Авторизация'); ?>

<a style = "float: left;" href="<?php echo SITE?>">Главная страница / </a>
<a style = "float: left; margin-left:5px;" href="">Авторизация</a>

<div class="mainCont">

	<div class="mainContText">
		<p><?php echo $data['cool'] ?></p>
		<p style = "color:red;"><?php echo $data['msgError'] ?></p></br>
	</div>
	


	<form action = "<?php echo SITE?>/enter" method = "POST">
		<table class = "enter">
			<tr>
				<td>
					<label name = "login">E-mail:</label>
				</td>
				<td>
					<input style = "width:400px" type = "text" name = "email" value = "<?php echo $_POST['email']?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label name = "pass">Пароль:</label>
				</td>
				<td>
					<input style = "width:400px" type="password" name="pass" />
				</td>
			</tr>
		</table>
		<input type = "submit" class="button" value = "Вход" />
	</form>
	
	<br><a class = "forgotpass" href="<?php echo SITE?>/forgotpass">Забыли пароль?</a>
</div>

<div class="mainContLink">
	<a href="<?php echo SITE?>">На главную</a></br></br>
	<a href="specification">Как это работает?</a></br></br>
	<a href="motivation">Почему это выгодно?</a></br></br>
	<a href="budget">Планирование покупок</a></br></br>
	<a href="bonus">Бонусы</a></br></br>
	<a href="planning">Ближайшие задачи</a></br></br>
</div>


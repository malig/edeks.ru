<h1>Восстановление пароля</h1>
<div class="mainCont">
	<div class="mainContText">

		<?php if($data['message']):?>
		  <p><?php echo $data['message']?></p>
		<?php endif; 
		  if($data['error']):?>
		  <p style = "color:red;"><?php echo $data['error']?></p>
		<?php endif;?>
		
			<?php 
			switch($data['form']){
			case 1: 
			?>   
			
			<p>На адрес электронной почты будет отправлена инструкция по восстановлению пароля.</p><br>
	</div>
			<form action = "<?php echo SITE?>/forgotpass" method = "POST">
				<table class = "enter">
					<tr>
						<td>
							<label name = "login">E-mail:</label>
						</td>
						<td>
							<input style = "width:300px" type = "text" name = "email" value="" />
						</td>
					</tr>
				</table>
				<input type = "submit" name="forgotpass" class="button" value = "Отправить" />
			</form>

			<?php 
			break;
			case 2: 
			?>  
			<form action="<?php echo SITE?>/forgotpass" method="POST">
			
				<table class = "enter">
					<tr>
						<td>
							<label name = "login">Новый пароль:</label>
						</td>
						<td>
							<input style = "width:300px" type = "password" name = "newPass"/>
						</td>
					</tr>
					<tr>
						<td>
							<label name = "pass">Подтвердите пароль:</label>
						</td>
						<td>
							<input style = "width:300px" type="password" name="pass2"/>
						</td>
					</tr>
				</table>
			
				<input type = "submit" class="button" name="chengePass" value = "Сохранить">
			</form>

			<?php } ?>
</div>
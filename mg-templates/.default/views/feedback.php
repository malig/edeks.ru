<?php mgTitle('Обратная связь');?>
<h1>
	Обратная связь
	<div class="errorSend">
		<?php	
		if($data['error']){
		  echo $data['error'];
		}
		?>
	</div>
</h1>

<?php if($data['dislpayForm']){ ?>
 <div class="mainCont">
  <form action="" method="post">
    <table class="table_order_form">
      <tr>
        <td>Ф.И.О.</td>
        <td><input type="text" name="fio" value="<?php echo $_POST['fio'] ?>"/></td>
      </tr>
      <tr>
        <td>E-mail<span style="color: red;">*</span></td>
        <td><input type="text" name="email" value="<?php echo $_POST['email'] ?>"/></td>
      </tr>
      <tr>
        <td>Сообщение:</td>
        <td><textarea name="message"><?php echo $_POST['message'] ?></textarea></td>
      </tr>
    </table>
    <br>
    <input type="submit" name="send" class="btn" value="Отправить сообщение">
  </form>
 </div>
  <?php
}else{
  echo $data['message'];
};
?>
<?php mgTitle('Обратная связь');?>
<a style = "float: left;" href="<?php echo SITE?>">Главная страница / </a>
<a style = "float: left; margin-left:5px;" href="">Обратная связь</a>


<?php if($data['dislpayForm']){ ?>
 <div class="mainCont">
 
 	<div class="mainContText">
		<p>Здесь вы можете оставить своё мнение о нас, а также сообщить о всех найденных ошибках и недостатках в нашей работе.</p></br>
	</div>
 
  <div class="errorSend">
    <?php 
    if($data['error']){
      echo $data['error'].'</br></br>';
    }
    ?>
  </div>

  <form action="" method="post">
    <table class = "enter">
      <tr>
        <td><label>Ф.И.О.</label></td>
        <td><input type="text" style = "width:500px" name="fio" value="<?php echo $_POST['fio'] ?>"/></td>
      </tr>
      <tr>
        <td><label>E-mail</label><span style="color: red;">*</span></td>
        <td><input type="text" style = "width:500px" name="email" value="<?php echo $_POST['email'] ?>"/></td>
      </tr>
      <tr>
        <td><label>Сообщение:</label><span style="color: red;">*</span></td>
        <td><textarea style = "width:500px" name="message"><?php echo $_POST['message'] ?></textarea></td>
      </tr>
    </table>
    <br>
    <input type="submit" name="send" class="button" value="Отправить сообщение">
  </form>
 </div>
 <div class="mainContLink">
  <a href="<?php echo SITE?>">На главную</a></br></br>
  <a href="specification">Как это работает?</a></br></br>
  <a href="motivation">Почему это выгодно?</a></br></br>
  <a href="budget">Планирование покупок</a></br></br>
  <a href="bonus">Бонусы</a></br></br>
  <a href="planning">Ближайшие задачи</a></br></br>
</div>
  <?php
}else{
  echo '<p style = "clear:both; margin-left:15px">'.$data['message'].'</p>';
};
?>
<?php mgTitle('Регистрация'); ?>

<h1>Регистрация</h1>
<?php
if($data['isRegistered']){
  echo "Вы успешно зарегистрировались";
}
else{
?>

  <?php echo $data['$msgError'] ?>

  <div class="mainCont">
    <form action = "<?php echo SITE?>/registration" method = "POST">
      <table>
        <tr>
          <td>E-mail:</td>
          <td><input type = "text" name = "email" value = "<?php echo $_POST['email']?>" /></td>
        </tr>
        <tr>
          <td>Пароль:</td>
          <td><input type="password" name="pass" /></td>
        </tr>
        <tr>
          <td>Подтвердите пароль:</td>
          <td><input type="password" name="pass2" /></td></td>
        </tr>
        <tr>
          <td>Имя:</td>
          <td><input type="text" name="name" value = "<?php echo $_POST['name']?>" /></td>
        </tr>
        <tr>
          <td>Фамилия:</td>
          <td><input type="text" name="sname" value = "<?php echo $_POST['sname']?>"/></td>
        </tr>
        <tr>
          <td>Телефон:</td>
          <td><input type="text" name="phone"  value = "<?php echo $_POST['phone']?>"/></td>
        </tr>
        <tr>
          <td>Адрес:</td>
          <td><input type="text" name="address" value = "<?php echo $_POST['address']?>" /></td>
        </tr>
      </table>
      <input type = "submit" class="btn" value = "Зарегистрироваться" />
    </form>
  </div>

<?php } ?>